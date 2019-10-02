<?php

/**
* Copyright 2019, dxw
*
* This file is part of dxw/codeception-notify-module.
*
* dxw/codeception-notify-module is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.

* dxw/codeception-notify-module is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.

* You should have received a copy of the GNU General Public License
* along with dxw/codeception-notify-module.  If not, see <https://www.gnu.org/licenses/>.
*/

namespace dxw\Codeception\Module;

use Mcustiel\Phiremock\Client\Phiremock;
use Mcustiel\Phiremock\Client\Utils\A;
use Mcustiel\Phiremock\Client\Utils\Is;
use Mcustiel\Phiremock\Client\Utils\Respond;

class Notify extends \Codeception\Module\Phiremock
{
    private $phiremock;
    
    const EMAIL_ENDPOINT = '/v2/notifications/email';
    
    public function expectEmailRequestWithSuccessResponse($body = '{"foo":"bar"}')
    {
        $this->expectARequestToRemoteServiceWithAResponse(
            Phiremock::on(
                A::postRequest()->andUrl(Is::equalTo(self::EMAIL_ENDPOINT))
            )->then(
                Respond::withStatusCode(200)->andBody($body)
            )
        );
    }
    
    public function expectEmailRequestWithFailureResponse(int $code = 401, string $body = '{"errors":[{"error":"foo", "message":"bar"}]}')
    {
        $this->expectARequestToRemoteServiceWithAResponse(
            Phiremock::on(
                A::postRequest()->andUrl(Is::equalTo(self::EMAIL_ENDPOINT))
            )->then(
                Respond::withStatusCode($code)->andBody($body)
            )
        );
    }
    
    public function getRecipientEmailAddresses()
    {
        $APIrequests = $this->grabRequestsMadeToRemoteService(A::postRequest()->andUrl(Is::equalTo(self::EMAIL_ENDPOINT)));
        $emailAddresses = [];
        foreach ($APIrequests as $APIrequest) {
            $emailAddresses[] = json_decode($APIrequest->body)->email_address;
        }
        return $emailAddresses;
    }
    
    public function seeLastEmailWasSentTo(string $emailAddress)
    {
        $emailAddresses = $this->getRecipientEmailAddresses();
        $lastEmailAddress = array_pop($emailAddresses);
        $this->assertEquals($emailAddress, $lastEmailAddress);
    }
    
    public function seeNotifyReceivedEmailRequests(int $numberOfRequests)
    {
        $this->seeRemoteServiceReceived($numberOfRequests, A::postRequest()->andUrl(Is::equalTo(self::EMAIL_ENDPOINT)));
    }
}
