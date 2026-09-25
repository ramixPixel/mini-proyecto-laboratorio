<?php
class OrderEvents
{
    /** @var OrderObserver[] */
    private array $observers = [];

    public function subscribe(OrderObserver $observer): void
    {
        $this->observers[] = $observer;
    }

    public function notify(Order $order): void
    {
        foreach ($this->observers as $observer) {
            $observer->update($order);
        }
    }

    public function pedidoCreado(Order $order): void
    {
        $this->notify($order);
    }
}
