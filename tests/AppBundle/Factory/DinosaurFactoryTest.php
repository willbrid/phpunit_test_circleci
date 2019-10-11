<?php

namespace Tests\AppBundle\Factory;

use AppBundle\Entity\Dinosaur;
use AppBundle\Factory\DinosaurFactory;
use AppBundle\Service\DinosaurLengthDeterminator;
use PHPUnit\Framework\TestCase;

class DinosaurFactoryTest extends TestCase
{
    /** @var DinosaurFactory */
    private $factory;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $lengthDeterminator;

    public function setUp()
    {
        $this->lengthDeterminator = $this->createMock(DinosaurLengthDeterminator::class);
        $this->factory = new DinosaurFactory($this->lengthDeterminator);
        // echo "\n SETUP \n";
    }

    /*

    public static function setUpBeforeClass()
    {
        echo "\n SETUPBEFORECLASS \n";
    }

    public function tearDown()
    {
        echo "\n TEARDOWN \n";
    }

    public static function tearDownAfterClass()
    {
        echo "\n TEARDOWNAFTERCLASS \n";
    }

    */

    public function testItGrowsAVelociraptor()
    {
        $dinosaur = $this->factory->growVelociraptor(5);

        $this->assertInstanceOf(Dinosaur::class, $dinosaur);
        $this->assertInternalType('string', $dinosaur->getGenus());
        $this->assertSame('Velociraptor', $dinosaur->getGenus());
        $this->assertSame(5, $dinosaur->getLength());
        // echo "\n Completed \n";
    }

    public function testItGrowsATriceratops()
    {
        $this->markTestIncomplete('Waiting confirmation from GenLab');
    }

    public function testItGrowsABabyVelociraptor()
    {
        if (!class_exists('Nanny')) {
            $this->markTestSkipped('There is nobody to watch the baby.');
        }

        $dinosaur = $this->factory->growVelociraptor(1);
        $this->assertSame(1, $dinosaur->getLength());
    }

    /**
     * @dataProvider getSpecificationTests
     */
    public function testItGrowsADinosaurFromASpecification(string $spec, /*bool $expectedIsLarge,*/ bool $expectedIsCarnivorous)
    {
        $this->lengthDeterminator
            ->expects($this->once())
            ->method('getLengthFromSpecification')
            ->with($spec)
            ->willReturn(20);

        $dinosaur = $this->factory->growFromSpecification($spec);

        /*
        if ($expectedIsLarge) {
            $this->assertGreaterThanOrEqual(Dinosaur::LARGE, $dinosaur->getLength());
        } else {
            $this->assertLessThanOrEqual(Dinosaur::LARGE, $dinosaur->getLength());
        }
        */

        $this->assertSame($expectedIsCarnivorous, $dinosaur->isCarnivorous(), 'Diets do not match');
        $this->assertSame(20, $dinosaur->getLength());
    }

    public function getSpecificationTests()
    {
        return [
            ['large carnivorous dinosaur', true /*, true*/],
            'default response' => ['give me all the cookies!!!', false /*, false*/],
            ['large herbivore', false /*, false*/],
        ];
    }

    /*
     * @dataProvider getHugeDinosaurSpecTests
     /
    public function testItGrowsAHugeDinosaur(string $specification)
    {
        $dinosaur = $this->factory->growFromSpecification($specification);

        $this->assertGreaterThanOrEqual(Dinosaur::HUGE, $dinosaur->getLength());
    }

    public function getHugeDinosaurSpecTests()
    {
        return [
            ['huge dinosaur'],
            ['huge dino'],
            ['huge'],
            ['OMG'],
        ];
    }
    */
}