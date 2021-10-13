<?php


namespace  Drupal\commerce_mpesa\Controller;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class Mpesa implements ContainerInjectionInterface
{

  /**
   * The current request.
   *
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $currentRequest;

  /**
   * Constructs a new DummyRedirectController object.
   *
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   The request stack.
   */
  public function __construct(RequestStack $request_stack) {
    $this->currentRequest = $request_stack->getCurrentRequest();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('request_stack')
    );
  }

  public function mpesa_stk(){
    $cancel = $this->currentRequest->request->get('cancel');
    $return = $this->currentRequest->request->get('return');
    //$total = $this->currentRequest->request->get('total');

    return array(
      '#theme' => 'mpesa_tpl',
      '#complete_url' =>$return
    );

  }
}
