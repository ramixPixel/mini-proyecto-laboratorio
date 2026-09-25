<?php
class OrderService
{
    public function __construct(
        private OrderValidator $validator,
        private OrderPricer $pricer,
        private OrderRepository $repository,
        private OrderNotifier $notifier,
        private OrderReporter $reporter,
        private OrderEvents $events
    ) {}

    public function createOrder(Order $order, string $tipoNotificacion, string $destino): void
    {
        $this->validator->validate($order);
        $total = $this->pricer->calculate($order);
        $order->amount = $total;
        $this->repository->save($order);
        $this->notifier->send($order, $tipoNotificacion, $destino);
        $this->events->pedidoCreado($order);
        echo $this->reporter->generate($order);
    }
}
