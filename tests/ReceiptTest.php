<?php
namespace TDD\Test;
require dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR .'autoload.php';

use PHPUnit\Framework\TestCase;
use TDD\Receipt;

/*Siin luuakse Receipt*/
class ReceiptTest extends TestCase {
    public function setUp() {
        $this->Receipt = new Receipt();
    }

    public function tearDown() {
        unset($this->Receipt);
    }

    /*Siin võrreldakse, kas input ja output on võrdsed. Tuleb teade*/

    public function testTotal() {
        $input = [0,2,5,8];
        $output = $this->Receipt->total($input);
        $this->assertEquals(
            15,
            $output,
            'When summing the total should equal 15'
        );
    }
/* Lisatakse kupong koodi. Antakse teada lõppsummast */
    public function testTotalAndCoupon() {
        $input = [0,2,5,8];
        $coupon = 0.20;
        $output = $this->Receipt->total($input, $coupon);
        $this->assertEquals(
            15,
            $output,
            'When summing the total should equal 15'
        );
    }

    public function testPostTaxTotal() {
        $Receipt = $this->getMockBuilder('TDD\Receipt')
            ->setMethods(['tax', 'total'])
            ->getMock();
        $Receipt->method('total')
            ->will($this->returnValue(10.00));
        $Receipt->method('tax')
            ->will($this->returnValue(1.00));
        $result = $Receipt->postTaxTotal([1,2,5,8], 0.20, null);
        $this->assertEquals(11.00, $result);
    }

/*Siin lisatakse summale maksuosa ja antakse. Tuleb teade summa kohta. */
    public function testTax() {
        $inputAmount = 10.00;
        $taxInput = 0.10;
        $output = $this->Receipt->tax($inputAmount, $taxInput);
        $this->assertEquals(
            1.00,
            $output,
            'The tax calculation should equal 1.00'
        );
    }
}