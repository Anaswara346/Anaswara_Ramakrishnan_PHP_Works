<?php 

enum Status: string {
    case Pending = "pending";
    case Shipped = "shipped";
    case Delivered = "delivered";

    public function label(): string {
        return ucfirst($this->value);
    }
}

class Order {
    public function __construct(public int $id, public Status $status) {}

}

$o = new Order(101, Status::Shipped);
echo "Order $o->id is " . $o->status->label();