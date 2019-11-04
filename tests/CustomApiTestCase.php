<?php declare(strict_types=1);

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\DataFixtures\PostFixtures;
use App\DataFixtures\UserFixtures;
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
    private $entityManager;

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
            new PostFixtures()
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

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseHeaderSame('Content-type', 'application/json');
        $responseData = json_decode($result->getContent(), true);

        return $responseData['token'];
    }

    /**
     * Send authorized request as SIMPLE_USER_ROLE
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

        $options['headers'] = $options['headers'] ?? [];
        $options['headers']['Authorization'] = sprintf('Bearer %s', $accessToken);
        $options['headers']['Accept'] = 'application/json';

        return static::createClient()->request($method, $url, $options);
    }
}
