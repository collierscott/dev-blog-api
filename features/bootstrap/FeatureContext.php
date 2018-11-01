<?php

use App\DataFixtures\AppFixtures;
use Behat\Gherkin\Node\PyStringNode;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\ORM\Tools\ToolsException;

class FeatureContext extends \Behatch\Context\RestContext
{
    const USERS = [
        'admin' => 'passWord1'
    ];
    const AUTH_URL = '/api/login_check';
    const AUTH_JSON = '
        {
            "username": "%s",
            "password": "%s"
        }
    ';

    /** @var \Behatch\HttpCall\Request $request */
    protected $request;
    private $fixtures;
    private $matcher;
    private $em;

    public function __construct(
        \Behatch\HttpCall\Request $request,
        AppFixtures $fixtures,
        EntityManagerInterface $em
    )
    {
        parent::__construct($request);
        $this->fixtures = $fixtures;
        $this->matcher = (new \Coduo\PHPMatcher\Factory\SimpleFactory())->createMatcher();
        $this->em = $em;
    }

    /**
     * @Given I am authenticated as :username
     */
    public function iAmAuthenticatedAs($username)
    {
        $this->request->setHttpHeader('Content-Type', 'application/ld+json');
        $this->request->send(
            'POST',
            $this->locatePath(self::AUTH_URL),
            [],
            [],
            sprintf(self::AUTH_JSON, $username, self::USERS[$username])
        );
        $json = json_decode($this->request->getContent(), true);
        // Make sure the token was returned
        $this->assertTrue(isset($json['token']));
        $token = $json['token'];
        $this->request->setHttpHeader(
            'Authorization',
            'Bearer '.$token
        );

    }

    /**
     * @Then the JSON matches expected template:
     */
    public function theJsonMatchesExpectedTemplate(PyStringNode $json)
    {
        $actual = $this->request->getContent();

        var_dump($actual);

        $this->assertTrue(
            $this->matcher->match($actual, $json->getRaw())
        );
    }

    /**
     * @BeforeScenario @createSchema
     * @throws ToolsException
     */
    public function createSchema()
    {
        // Get entity metadata
        $classes = $this->em->getMetadataFactory()->getAllMetadata();

        // Drop and create schema
        $schemaTool = new SchemaTool($this->em);
        $schemaTool->dropSchema($classes);
        $schemaTool->createSchema($classes);

        // Load fixtures and execute
        $purger = new ORMPurger($this->em);
        $fixturesExecutor = new ORMExecutor($this->em, $purger);
        $fixturesExecutor->execute([
            $this->fixtures
        ]);
    }

    /**
     * @BeforeScenario @image
     */
    public function prepareImages()
    {
        copy(
            __DIR__.'/../fixtures/kim-jong.jpg',
            __DIR__.'/../fixtures/files/kim-jong.jpg'
        );
    }
}
