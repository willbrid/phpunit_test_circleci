<?php

namespace Tests\AppBundle\Service;

use AppBundle\Entity\Dinosaur;
use AppBundle\Service\DinosaurLengthDeterminator;
use PHPUnit\Framework\TestCase;

class DinosaurLengthDeterminatorTest extends TestCase
{
    /**
     * @dataProvider getSpecLengthTests
     */
    public function testItReturnsCorrectLengthRange($spec, $minExpectedSize, $maxExpectedSize)
    {
        $determinator = new DinosaurLengthDeterminator();
        $expectedSize = $determinator->getLengthFromSpecification($spec);

        $this->assertGreaterThanOrEqual($minExpectedSize, $expectedSize);
        $this->assertLessThanOrEqual($maxExpectedSize, $expectedSize);
    }

    public function getSpecLengthTests()
    {
        return [
            // Specification, min length, max length
            ['large carnivorous dinosaur', Dinosaur::LARGE, Dinosaur::HUGE - 1],
            ['give me all the cookies!!!', 0, Dinosaur::LARGE - 1],
            ['large herbivore', Dinosaur::LARGE, Dinosaur::HUGE - 1],
            ['huge dinosaur', Dinosaur::HUGE, 100],
            ['huge dino', Dinosaur::HUGE, 100],
            ['huge', Dinosaur::HUGE, 100],
            ['OMG', Dinosaur::HUGE, 100]
        ];
    }
}