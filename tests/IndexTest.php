<?php
use PHPUnit\Framework\TestCase;

class IndexTest extends TestCase
{
    public function testIndexLoadsCorrectly()
    {
        ob_start();
        include 'public/index.php';
        $output = ob_get_clean();
        $this->assertNotEmpty($output, "The index.php should return content.");
    }
}
