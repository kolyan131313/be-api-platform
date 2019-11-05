<?php declare(strict_types=1);

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\DataFixtures\MediaObjectFixtures;
use App\DataFixtures\PostFixtures;
use App\DataFixtures\UserFixtures;
use App\DataFixtures\VerificationRequestFixtures;
use App\Entity\MediaObject;
use App\Entity\Post;
use App\Entity\User;
use App\Entity\VerificationRequest;
use App\Repository\MediaObjectRepository;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use App\Repository\VerificationRequestRepository;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

abstract class CustomApiTestCase extends ApiTestCase
{
    /**
     * @var KernelBrowser $client
     */
    protected $client;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * Set up services before class
     */
    public static function setUpBeforeClass(): void
    {
        $kernel = static::createKernel();
        $kernel->boot();
        /** @var EntityManager $entityManager */
        $entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        /** @var UserPasswordEncoderInterface $passwordEncoder */
        $passwordEncoder = $kernel->getContainer()->get('security.password_encoder');

        $loader = new Loader();

        foreach (self::getFixtures($passwordEncoder) as $fixture) {
            $loader->addFixture($fixture);
        }

        $purger = new ORMPurger();
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_DELETE);
        $executor = new ORMExecutor($entityManager, $purger);
        $executor->execute($loader->getFixtures());
    }

    /**
     * Tear down
     */
    protected function tearDown(): void
    {
        if ($this->entityManager->getConnection()->isTransactionActive()) {
            $this->entityManager->rollback();
        }
    }

    /**
     * Set up
     */
    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->disableReboot();

        $this->entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');
        $this->entityManager->beginTransaction();
        $this->entityManager->getConnection()->setAutoCommit(false);
    }

    /**
     * Get list of fixtures
     *
     * @param UserPasswordEncoderInterface $passwordEncoder
     *
     * @return iterable
     */
    private static function getFixtures(UserPasswordEncoderInterface $passwordEncoder): iterable
    {
        return [
            new UserFixtures($passwordEncoder),
            new PostFixtures(),
            new MediaObjectFixtures(),
            new VerificationRequestFixtures()
        ];
    }

    /**
     * Get Bearer acess token for user
     *
     * @param string $username
     * @param string $password
     *
     * @return string
     * @throws TransportExceptionInterface
     */
    public function getBearerAccessToken(string $username, string $password): string
    {
        $result = static::createClient()->request('POST', '/api/login', ['json' => [
            'username' => $username,
            'password' => $password
        ]]);

        static::assertResponseStatusCodeSame(Response::HTTP_OK);
        static::assertResponseHeaderSame('Content-type', 'application/json');
        $responseData = json_decode($result->getContent(), true);

        return $responseData['token'];
    }

    /**
     * Send authorized request as SIMPLE_USER role
     *
     * @param string $method
     * @param string $url
     * @param array $options
     *
     * @return ResponseInterface
     *
     * @throws TransportExceptionInterface
     */
    public function sendAuthenticatedRequestAsSimpleUser(
        string $method,
        string $url,
        array $options = []
    ): ResponseInterface
    {
        $accessToken = $this->getBearerAccessToken(UserFixtures::SIMPLE_USER_EMAIL, UserFixtures::DEFAULT_PASSWORD);

        return $this->sendAuthenticatedRequest($method, $url, $accessToken, $options);
    }

    /**
     * Send authorized request as BLOGGER as blogger
     *
     * @param string $method
     * @param string $url
     * @param array $options
     *
     * @return ResponseInterface
     *
     * @throws TransportExceptionInterface
     */
    public function sendAuthenticatedRequestAsBlogger(
        string $method,
        string $url,
        array $options = []
    ): ResponseInterface
    {
        $accessToken = $this->getBearerAccessToken(UserFixtures::BLOGGER_USER_EMAIL, UserFixtures::DEFAULT_PASSWORD);

        return $this->sendAuthenticatedRequest($method, $url, $accessToken, $options);
    }

    /**
     * Send authorized request as BLOGGER as blogger
     *
     * @param string $method
     * @param string $url
     * @param array $options
     *
     * @return ResponseInterface
     *
     * @throws TransportExceptionInterface
     */
    public function sendAuthenticatedRequestAsAdmin(
        string $method,
        string $url,
        array $options = []
    ): ResponseInterface
    {
        $accessToken = $this->getBearerAccessToken(UserFixtures::ADMIN_USER_EMAIL, UserFixtures::DEFAULT_PASSWORD);

        return $this->sendAuthenticatedRequest($method, $url, $accessToken, $options);
    }

    /**
     * @param string $method
     * @param string $url
     * @param string $accessToken
     * @param array $options
     * @return Response|ResponseInterface
     * @throws TransportExceptionInterface
     */
    private function sendAuthenticatedRequest(
        string $method,
        string $url,
        string $accessToken,
        array $options = []
    ): ResponseInterface
    {
        $options['headers'] = $options['headers'] ?? [];
        $options['headers']['Authorization'] = sprintf('Bearer %s', $accessToken);
        $options['headers']['Accept'] = 'application/json';

        return static::createClient()->request($method, $url, $options);
    }

    /**
     * @return UserRepository
     */
    public function getUserRepository(): UserRepository
    {
        return $this->entityManager->getRepository(User::class);
    }

    /**
     * @return PostRepository
     */
    public function getPostRepository(): PostRepository
    {
        return $this->entityManager->getRepository(Post::class);
    }

    /**
     * @return VerificationRequestRepository
     */
    public function getVerificationRequestRepository(): VerificationRequestRepository
    {
        return $this->entityManager->getRepository(VerificationRequest::class);
    }

    /**
     * @return MediaObjectRepository
     */
    public function getMediaObjectRepository(): MediaObjectRepository
    {
        return $this->entityManager->getRepository(MediaObject::class);
    }
}
