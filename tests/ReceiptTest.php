<?php
namespace TDD\Test;
require dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR .'autoload.php';

use PHPUnit\Framework\TestCase;
use TDD\Receipt;    /*alusfail Receipt */

    /*   Siin luuakse TestCase klass, mis on avalik meetod ning ligipääsetav teistest klassidest.*/
class ReceiptTest extends TestCase {
    public function setUp() {
        $this->Receipt = new Receipt(); /*Luuakse uus objekt nimega Receipt. */
    }
    /*   Dummy object- Üks osa TestCase klassist. */
    public function tearDown() {

    /*   Objekt $Receipt kustutatakse mälust. */
        unset($this->Receipt);
    }

    /*   @dataProvider provideTotal viitab, et järgnevas meetodis kasutatakse testiandmeid,
         mille tagastab meetod provideTotal()
         Siin arvutattakse kokku lõpusummat. Lisatakse arvutuskäiku ka kupong
         Kontrollitakse, kas $Receipt objekti meetod total() tagastab õige väärtuse.
         @dataProvider provideTotal */
    public function testTotal($items, $expected) {
        $coupon = null;

    /*   Luuakse muutuja, millesse salvestatakse $Receipt objekti meetodi total() poolt tagastatud väärtus.
         Kuna kasutusel on @dataProvider, siis massiivi $items väärtused saadakse meetodist provideTotal(),
         ehk andmed [1,2,5,8], [-1,2,5,8] ning [1,2,8] */
        $output = $this->Receipt->total($items, $coupon);

    /*   Kinnitatakse testTotal eeldatav väärtus, kas oodatud väärtus võrdub tagastatava väärtusega*/
        $this->assertEquals(

    /*   Oodatavad väärtused; sarnaselt $items väärtustele saadakse $expected väärtused provideTotal() meetodist,
         siin on need 16, 14, 11. Seega käivitatakse assertEquals() kolm korda. */
            $expected,

    /*   Tegelikult tagastatavad väärtused */
            $output,

    /*   Veateade tuleb, kui oodatav väärtus ei ole võrdu tagastatava väärtusega. */
            "When summing the total should equal {$expected}"
        );
    }
    /*   Andmeedastuse funktsiooni lisamine, mis sätestab erinevad sisendväärtused*/
    public function provideTotal() {
        return [
            'ints totaling 16' => [[1,2,5,8], 16],
            [[-1,2,5,8], 14],
            [[1,2,8], 11],
        ];
    }

    /*   Siin kontrollitakse, kas summa ning summale lisatud kupongi arvutamise korrektset toimimist. */
    public function testTotalAndCoupon() {
        $input = [0,2,5,8];
        $coupon = 0.20;
        $output = $this->Receipt->total($input, $coupon);
        $this->assertEquals(
            12,
            $output,

    /*   Veateade tuleb, kui oodatav väärtus ei ole võrdu tagastatava väärtusega. */
            'When summing the total should equal 12'
        );
    }

    /*   Siin kontrollitakse Mock objekti abil, kas kogusummale maksu lisamine toimib õigesti. */
    public function testPostTaxTotal() {
        $items = [1,2,5,8];
        $tax = 0.20;
        $coupon = null;

    /*     getMockBuilder() meetod loob Receipt klassi põhjal Mock-objekti */
        $Receipt = $this->getMockBuilder('TDD\Receipt')

    /*  Mock objektile lisatakse meetodid tax ja total. */
            ->setMethods(['tax', 'total'])

    /*  Luuakse Mock-objekt, millele antakse $Receipt omadused. */
            ->getMock();

    /*  Loodud Mock objektilt eeldatakse, et meetod total)kutsutakse välja üks kord.  */
        $Receipt->expects($this->once())

    /*  Määratakse väljakutsutav mock objekti meetod. */
            ->method('total')

    /*  Määratakse esitatavad argumendid. */
            ->with($items, $coupon)

    /*  Fikseeritaakse, et mock objekti meetod tagastab väärtuse õige väärtuse, siin on selleks 10.0 */
            ->will($this->returnValue(10.00));

    /*   Loodud mock objektilt eeldatakse, et allpool määratud meetod (tax) kutsutakse välja üks kord.
         Vastavate argumentidega ning määratakse tagastatav väärtus 1.00   */
        $Receipt->expects($this->once())
            ->method('tax')
            ->with(10.00, $tax)
            ->will($this->returnValue(1.00));

    /*   Kutsutakse välja loodud Mock objekti meetod postTaxTotal() vastavate argumentidega. */
        $result = $Receipt->postTaxTotal([1,2,5,8], 0.20, null);

    /*   Vaadatakse, et kutsuti välja Mock objekti meetod total(), mis tagastas väärtuse 10.00
         ja sama mock objekti meetod tax(), mis tagastas väärtuse 1.00.
         Vaadatakse, et mõlemad kutsututi ikka välja ainult üks kord - expects($this->once())
         Kui liita aga kokku 105. real olevad massiiviliikmed 1,2,5,8 ning summast (16) lahutada maha kupongi väärtus (16-16*0.2),
         siis saame tulemuseks 16-3.2=12.8. Seega, kui oleks tegu reaalse Receipt klassi objektiga, siis real 115 olev assertEquals()
         tagastaks "false" ehk testi tulemuseks oleks veateada (kuna 11.00 ei võrdu 12.8)
         Kuna antud testis on kasutusel mock objekt, siis testitakse ainult tingimusi, mis on kirjeldatud real 107-108 ning
         seetõttu assertEquals(11.00, $result) tagastab "true". */
        $this->assertEquals(11.00, $result);
    }

    /*   Kontrollitakse summale maksuosa lisamise korrektset toimimist. */
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