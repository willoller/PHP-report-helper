<?php

require_once('report.php');

class ReportTest extends PHPUnit_Framework_TestCase
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

    public function testTotal()
    {
        $report = new Report($this->returnedData());

        $this->assertEquals( 31,   $report->total("amount") );
        $this->assertEquals( null, $report->total("status") );
    }

    public function testTotalException()
    {
        $report = new Report($this->returnedData());

        try
        {
            $this->assertEquals( null, $report->total() );
            $this->assertTrue(false);
        }
        catch (Exception $e)
        {
            $this->assertTrue(true);
        }
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

    public function testSubsetTotals()
    {
        $report = new Report($this->returnedData());

        $this->assertEquals( 24, $report->status("off")->total("amount") );
        $this->assertEquals( 16, $report->name("e")->total("amount") );
    }
}
