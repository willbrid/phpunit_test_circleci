<?php

namespace Tests\AppBundle\Controller;

use AppBundle\DataFixtures\ORM\LoadBasicParkData;
use AppBundle\DataFixtures\ORM\LoadSecurityData;
use Doctrine\Common\DataFixtures\Executor\AbstractExecutor;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\DomCrawler\Crawler;

class DefaultControllerTest extends WebTestCase
{
    /** @var AbstractExecutor $fixtures */
    private $executor;

    /** @var Client */
    private $client;

    /** @var Crawler crawler */
    private $crawler;

    protected function setUp()/* The :void return type declaration that should be here would cause a BC issue */
    {
        $this->executor = $this->loadFixtures([
            LoadBasicParkData::class,
            LoadSecurityData::class
        ]);

        $this->client = $this->makeClient();
        $this->crawler = $this->client->request('GET', '/');
    }

    public function testEnclosuresAreShownOnHomepage()
    {
        $this->assertStatusCode(200, $this->client);

        $table = $this->crawler->filter('.table-enclosures');
        $this->assertCount(3, $table->filter('tbody tr'));
    }

    public function testThatThereIsAnAlarmButtonWithoutSecurity()
    {
        $fixtures = $this->executor->getReferenceRepository();

        $enclosure = $fixtures->getReference('carnivorous-enclosure');
        $selector = sprintf('#enclosure-%s .button-alarm', $enclosure->getId());

        $this->assertGreaterThan(0, $this->crawler->filter($selector)->count());
    }

    public function testItGrowsADinosaurFromSpecification()
    {
        $this->client->followRedirects();

        $form = $this->crawler->selectButton('Grow dinosaur')->form();
        $form['enclosure']->select(3);
        $form['specification']->setValue('large herbivore');

        $this->client->submit($form);

        $this->assertContains(
            'Grew a large herbivore in enclosure #3',
            $this->client->getResponse()->getContent()
        );
    }
}