<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Microsoft\Graph\Graph;

class SendEmail implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
  public $to;
  public $subject;
  public $body;

  public function __construct($subject, $body, $to)
  {
    $this->subject = $subject;
    $this->body = $body;
    $this->to = $to;
  }

  public function getAccessToken()
  {
    $tenantId = env('MICROSOFT_GRAPH_TENANT_ID');
    $clientId = env('MICROSOFT_GRAPH_CLIENT_ID');
    $clientSecret = env('MICROSOFT_GRAPH_CLIENT_SECRET');
    $accessToken = json_decode((new \GuzzleHttp\Client())->post(
      'https://login.microsoftonline.com/' . $tenantId . '/oauth2/v2.0/token',
      [
        'form_params' => [
          'client_id' => $clientId,
          'client_secret' => $clientSecret,
          'scope' => 'https://graph.microsoft.com/.default',
          'grant_type' => 'client_credentials',
        ],
      ]
    )->getBody()->getContents())->access_token;
    return $accessToken;
  }

  public function handle()
  {
    $graph = new Graph();
    $graph->setAccessToken($this->getAccessToken());
    $user = $graph->createRequest("GET", "/users")->execute();
    $userId = $user->getBody()["value"][0]['id'];

    $sendMailBody = [
      'message' => array(
        'subject' => $this->subject,
        'body' => array('content' => $this->body, 'contentType' => "HTML"),
        'toRecipients' => array(array('emailAddress' => array('address' => $this->to)))
      )
    ];
    return $graph->createRequest('POST', "/users/{$userId}/sendMail")->attachBody($sendMailBody)->execute();
  }
}
