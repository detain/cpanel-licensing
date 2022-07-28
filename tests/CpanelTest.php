<?php

use Detain\Cpanel\Cpanel;
use PHPUnit\Framework\TestCase;

/**
 * Class CpanelTest
 */
class CpanelTest extends TestCase
{
    /**
     * @var Cpanel
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Cpanel(getenv('CPANEL_LICENSING_USERNAME'), getenv('CPANEL_LICENSING_PASSWORD'));
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Detain\Cpanel\Cpanel::setopt
     */
    public function testSetopt()
    {
        $prev = $this->object->opts;
        $data = 'Test Agent';
        $this->object->setopt(CURLOPT_USERAGENT, $data);
        $this->assertNotEquals($prev, $this->object->opts, 'Making sure it changes');
        $this->assertEquals($this->object->opts[CURLOPT_USERAGENT], $data, 'Making sure the new opt is set');
        $this->object->opts = $prev;
    }

    /**
     * @covers Detain\Cpanel\Cpanel::setCredentials
     */
    public function testSetCredentials()
    {
        $prev = $this->object->opts;
        $user = 'username';
        $pass = 'password';
        $this->object->setCredentials($user, $pass);
        $this->assertEquals($this->object->opts[CURLOPT_USERPWD], $user.':'.$pass, 'Making sure the credentials set');
        $this->object->opts = $prev;
    }

    /**
     * @covers Detain\Cpanel\Cpanel::setFormat
     * @todo   Implement testSet_format().
     */
    public function testSet_format()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Detain\Cpanel\Cpanel::reactivateLicense
     * @todo   Implement testReactivateLicense().
     */
    public function testReactivateLicense()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Detain\Cpanel\Cpanel::expireLicense
     * @todo   Implement testExpireLicense().
     */
    public function testExpireLicense()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Detain\Cpanel\Cpanel::extendOnetimeUpdates
     * @todo   Implement testExtendOnetimeUpdates().
     */
    public function testExtendOnetimeUpdates()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Detain\Cpanel\Cpanel::changeip
     * @todo   Implement testChangeip().
     */
    public function testChangeip()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Detain\Cpanel\Cpanel::requestTransfer
     * @todo   Implement testRequestTransfer().
     */
    public function testRequestTransfer()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Detain\Cpanel\Cpanel::activateLicense
     * @todo   Implement testActivateLicense().
     */
    public function testActivateLicense()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Detain\Cpanel\Cpanel::addPickupPass
     * @todo   Implement testAddPickupPass().
     */
    public function testAddPickupPass()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Detain\Cpanel\Cpanel::registerAuth
     * @todo   Implement testRegisterAuth().
     */
    public function testRegisterAuth()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Detain\Cpanel\Cpanel::fetchGroups
     * @todo   Implement testFetchGroups().
     */
    public function testFetchGroups()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Detain\Cpanel\Cpanel::fetchLicenseRiskData
     * @todo   Implement testFetchLicenseRiskData().
     */
    public function testFetchLicenseRiskData()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Detain\Cpanel\Cpanel::fetchLicenseRaw
     * @todo   Implement testFetchLicenseRaw().
     */
    public function testFetchLicenseRaw()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Detain\Cpanel\Cpanel::fetchLicenseId
     * @todo   Implement testFetchLicenseId().
     */
    public function testFetchLicenseId()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Detain\Cpanel\Cpanel::fetchPackages
     * @todo   Implement testFetchPackages().
     */
    public function testFetchPackages()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Detain\Cpanel\Cpanel::fetchLicenses
     * @todo   Implement testFetchLicenses().
     */
    public function testFetchLicenses()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Detain\Cpanel\Cpanel::fetchExpiredLicenses
     * @todo   Implement testFetchExpiredLicenses().
     */
    public function testFetchExpiredLicenses()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Detain\Cpanel\Cpanel::findKey
     * @todo   Implement testFindKey().
     */
    public function testFindKey()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }
}
