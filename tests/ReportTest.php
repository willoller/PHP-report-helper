<?php

namespace YetAnoterLibrary\ReportHelper;

class ReportTest extends \PHPUnit_Framework_TestCase
{
    public function returnedData()
    {
        return array(
            array(
                "name"   => "a",
                "amount" => "1",
                "status" => "on",
            ),
            array(
                "name"   => "b",
                "amount" => "2",
                "status" => "on",
            ),
            array(
                "name"   => "c",
                "amount" => "4",
                "status" => "on",
            ),
            array(
                "name"   => "d",
                "amount" => "8",
                "status" => "off",
            ),
            array(
                "name"   => "e",
                "amount" => "16",
                "status" => "off",
            ),
        );
    }

    public function testIterator()
    {
        $report = new Report($this->returnedData());

        $count = 0;
        foreach ($report as $r) {
            $count++;
        }

        $this->assertEquals(5, $count);
    }

    public function testArrayAccessor()
    {
        $report = new Report($this->returnedData());

        $this->assertEquals("a", $report[0]->name);
        $this->assertEquals("e", $report[4]->name);

        $off = $report->status("off");
        $this->assertEquals("d", $off[0]->name);
    }

    public function testTotal()
    {
        $report = new Report($this->returnedData());

        $this->assertEquals( 31,   $report->total("amount") );
        $this->assertEquals( null, $report->total("status") );
    }

    public function testTotalException()
    {
        $report = new Report($this->returnedData());
        $this->assertEquals( null, $report->total() );
    }

    public function testCount()
    {
        $report = new Report($this->returnedData());

        $this->assertEquals( 5,  $report->count("amount") );
        $this->assertEquals( 5,  $report->count("status") );
        $this->assertEquals( 5,  $report->count() );
    }

    public function testSubset()
    {
        $report = new Report($this->returnedData());

        $expected = new Report(array(
            array(
                "name"   => "d",
                "amount" => "8",
                "status" => "off",
            ),
            array(
                "name"   => "e",
                "amount" => "16",
                "status" => "off",
            ),
        ));

        $this->assertEquals( $expected, $report->status("off") );
    }

    public function testExclude()
    {
        $report = new Report($this->returnedData());

        $expected = new Report(array(
            array(
                "name"   => "d",
                "amount" => "8",
                "status" => "off",
            ),
            array(
                "name"   => "e",
                "amount" => "16",
                "status" => "off",
            ),
        ));

        $this->assertEquals( $expected, $report->exclude('name', array('a', 'b', 'c')) );
    }

    public function testSubsetTotals()
    {
        $report = new Report($this->returnedData());

        $this->assertEquals( 24, $report->status("off")->total("amount") );
        $this->assertEquals( 16, $report->name("e")->total("amount") );
    }

    public function testReportCategoryGroups()
    {
        $report = new Report($this->returnedData());

        $expected = array(
            "on" => new Report(array(
                array(
                    "name"   => "a",
                    "amount" => "1",
                    "status" => "on",
                ),
                array(
                    "name"   => "b",
                    "amount" => "2",
                    "status" => "on",
                ),
                array(
                    "name"   => "c",
                    "amount" => "4",
                    "status" => "on",
                ),
            )),
            "off" => new Report(array(
                array(
                    "name"   => "d",
                    "amount" => "8",
                    "status" => "off",
                ),
                array(
                    "name"   => "e",
                    "amount" => "16",
                    "status" => "off",
                ),
            )),
        );

        $this->assertEquals( 2,  count($report->status) );
        $this->assertEquals( $expected, $report->status );
    }

    public function testAddFunctions()
    {
        $report = new Report($this->returnedData());

        $report->add('test_func', function($a, $b, $c) {
            return $a * $b * $c;
        });

        $this->assertEquals( 6,  $report->test_func(1, 2, 3));
        $this->assertEquals(24,  $report->test_func(2, 3, 4));
    }
}
