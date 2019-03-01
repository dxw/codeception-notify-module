<?php 

namespace dxw\Codeception\Module;

use Mcustiel\Phiremock\Client\Phiremock;
use Mcustiel\Phiremock\Client\Utils\A;
use Mcustiel\Phiremock\Client\Utils\Is;
use Mcustiel\Phiremock\Client\Utils\Respond;

class Notify extends \Codeception\Module\Phiremock 
{
    private $phiremock;
    
    const EMAIL_ENDPOINT = '/v2/notifications/email';
    
    public function expectEmailRequestWithSuccessResponse()
    {
        $this->expectARequestToRemoteServiceWithAResponse(
            Phiremock::on(
                A::postRequest()->andUrl(Is::equalTo(self::EMAIL_ENDPOINT))
            )->then(
                Respond::withStatusCode(200)->andBody('{"foo":"bar"}')
            )
        );
    }
    
    public function expectEmailRequestWithFailureResponse()
    {
        $this->expectARequestToRemoteServiceWithAResponse(
            Phiremock::on(
                A::postRequest()->andUrl(Is::equalTo(self::EMAIL_ENDPOINT))
            )->then(
                Respond::withStatusCode(401)->andBody('{"errors":[{"error":"foo", "message":"bar"}]}')
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
    
    private function expectEmailRequestWithResponse(int $responseCode)
    {
        $this->expectARequestToRemoteServiceWithAResponse(
            Phiremock::on(
                A::postRequest()->andUrl(Is::equalTo(self::EMAIL_ENDPOINT))
            )->then(
                Respond::withStatusCode($responseCode)->andBody('{"foo":"bar"}')
            )
        );
    }
}