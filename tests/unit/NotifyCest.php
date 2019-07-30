<?php

class NotifyCest
{
    public function _before(UnitTester $I)
    {
        $this->notifyClient = new \Alphagov\Notifications\Client([
            'apiKey' => 'key_name-1546058f-5a25-4334-85ae-e68f2a44bbaf-522ec739-ca63-4ec5-b082-08ce08ad65e2',
            'httpClient' => new \Http\Adapter\Guzzle6\Client,
            'baseUrl' => 'http://localhost:18080'
        ]);
    }

    // tests
    public function mockSuccessResponseDoesNotThrowException(UnitTester $I)
    {
        $I->expectEmailRequestWithSuccessResponse();
        $this->notifyClient->sendEmail(
            'betty@example.com',
            'df10a23e-2c0d-4ea5-87fb-82e520cbf93c'
        );
    }
    
    public function mockFailureResponseThrowsException(UnitTester $I)
    {
        $I->expectEmailRequestWithFailureResponse();
        $I->expectException(Alphagov\Notifications\Exception\ApiException::class, function () {
            $this->notifyClient->sendEmail(
                'betty@example.com',
                'df10a23e-2c0d-4ea5-87fb-82e520cbf93c'
            );
        });
    }
    
    public function getRecipientEmailAddressesReturnsEmptyArrayIfNoRequests(UnitTester $I)
    {
        $result = $I->getRecipientEmailAddresses();
        $I->assertEquals([], $result);
    }
    
    public function getRecipientEmailAddressesReturnsArrayOfOneAddressIfOneRequest(UnitTester $I)
    {
        $I->expectEmailRequestWithSuccessResponse();
        $this->notifyClient->sendEmail(
            'betty@example.com',
            'df10a23e-2c0d-4ea5-87fb-82e520cbf93c'
        );
        $result = $I->getRecipientEmailAddresses();
        $I->assertEquals(['betty@example.com'], $result);
    }
    
    public function getRecipientEmailAddressesReturnsArrayOfMultipleAddressesIfMultipleRequests(UnitTester $I)
    {
        $I->expectEmailRequestWithSuccessResponse();
        $this->notifyClient->sendEmail(
            'betty@example.com',
            'df10a23e-2c0d-4ea5-87fb-82e520cbf93c'
        );
        $this->notifyClient->sendEmail(
            'foo@bar.com',
            'df10a23e-2c0d-4ea5-87fb-82e520cbf93c'
        );
        $result = $I->getRecipientEmailAddresses();
        $I->assertEquals(['betty@example.com', 'foo@bar.com'], $result);
    }
    
    public function seeLastEmailWasSentToPassesIfMatchesLatestEmailAddress(UnitTester $I)
    {
        $I->expectEmailRequestWithSuccessResponse();
        $this->notifyClient->sendEmail(
            'betty@example.com',
            'df10a23e-2c0d-4ea5-87fb-82e520cbf93c'
        );
        $I->seeLastEmailWasSentTo('betty@example.com');
        $this->notifyClient->sendEmail(
            'foo@bar.com',
            'df10a23e-2c0d-4ea5-87fb-82e520cbf93c'
        );
        $I->seeLastEmailWasSentTo('foo@bar.com');
    }
    
    public function seeLastEmailWasSentToFailsIfDoesNotMatch(UnitTester $I)
    {
        $I->expectEmailRequestWithSuccessResponse();
        $this->notifyClient->sendEmail(
            'betty@example.com',
            'df10a23e-2c0d-4ea5-87fb-82e520cbf93c'
        );
        $I->expectException(PHPUnit\Framework\Exception::class, function () {
            $I->seeLastEmailWasSentTo('foo@bar.com');
        });
    }
    
    public function seeNotifyReceivedEmailRequestsPassesWithCorrectNumber(UnitTester $I)
    {
        $I->seeNotifyReceivedEmailRequests(0);
        $I->expectEmailRequestWithSuccessResponse();
        $this->notifyClient->sendEmail(
            'betty@example.com',
            'df10a23e-2c0d-4ea5-87fb-82e520cbf93c'
        );
        $I->seeNotifyReceivedEmailRequests(1);
        $this->notifyClient->sendEmail(
            'foo@bar.com',
            'df10a23e-2c0d-4ea5-87fb-82e520cbf93c'
        );
        $I->seeNotifyReceivedEmailRequests(2);
    }
    
    public function seeNotifyReceivedEmailRequestsFailsWithIncorrectNumber(UnitTester $I)
    {
        $I->expectException(PHPUnit\Framework\Exception::class, function () {
            $I->seeNotifyReceivedEmailRequests(1);
        });
        $I->expectEmailRequestWithSuccessResponse();
        $this->notifyClient->sendEmail(
            'betty@example.com',
            'df10a23e-2c0d-4ea5-87fb-82e520cbf93c'
        );
        $I->expectException(PHPUnit\Framework\Exception::class, function () {
            $I->seeNotifyReceivedEmailRequests(0);
        });
        $this->notifyClient->sendEmail(
            'foo@bar.com',
            'df10a23e-2c0d-4ea5-87fb-82e520cbf93c'
        );
        $I->expectException(PHPUnit\Framework\Exception::class, function () {
            $I->seeNotifyReceivedEmailRequests(3);
        });
    }
}
