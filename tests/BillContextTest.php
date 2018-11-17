<?php
/**
 * 
 */
namespace BillingBoss\Tests;


use BillingBoss\BillContext;
use PHPUnit\Framework\TestCase;

class BillContextTest extends TestCase
{

    public function testLeadingAndTrailingSpacesInStructureAreTrimmed()
    {
        $context = new BillContext(0, '    5  ');

        $this->assertEquals('5', $context->getStructure());
    }

    public function testEmbeddedSpacesInStructureAreReplacedWithASingleSpace()
    {
        $context = new BillContext(0, '1.0%     [30,         100]');
        $this->assertEquals('1.0% [30, 100]', $context->getStructure());

        $context->setStructure('5%,100             >      15%,   500 >  18.5%, *');
        $this->assertEquals('5%,100 > 15%, 500 > 18.5%, *', $context->getStructure());

    }

}