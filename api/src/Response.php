<?php

namespace CrosscutFestival;

use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

class Response extends JsonResponse {

  /**
   * @param string $message
   *   Error message to send back in response.
   *
   * @return $this
   */
  public function setError($message) {
    $this->setData(['error' => $message]);
    $this->setStatusCode(HttpResponse::HTTP_BAD_REQUEST);
    return $this;
  }

  /**
   * {@inheirtdoc}
   */
  public function send() {
    parent::send();
    // The API will only send a single response, so die after this.
    die;
  }

}
