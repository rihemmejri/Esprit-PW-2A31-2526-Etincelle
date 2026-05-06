<?php
// models/Notification.php
require_once __DIR__ . '/../config.php';

class Notification {
    private ?int $id_notification;
    private ?int $id_user;
    private ?string $type;
    private ?string $title;
    private ?string $message;
    private ?string $icon;
    private ?string $color;
    private ?bool $is_read;
    private ?string $created_at;

    public function __construct(
        ?int $id_notification = null,
        ?int $id_user = null,
        ?string $type = null,
        ?string $title = null,
        ?string $message = null,
        ?string $icon = 'fa-bell',
        ?string $color = '#4CAF50',
        ?bool $is_read = false,
        ?string $created_at = null
    ) {
        $this->id_notification = $id_notification;
        $this->id_user = $id_user;
        $this->type = $type;
        $this->title = $title;
        $this->message = $message;
        $this->icon = $icon;
        $this->color = $color;
        $this->is_read = $is_read;
        $this->created_at = $created_at;
    }

    // Getters
    public function getIdNotification(): ?int { return $this->id_notification; }
    public function getIdUser(): ?int { return $this->id_user; }
    public function getType(): ?string { return $this->type; }
    public function getTitle(): ?string { return $this->title; }
    public function getMessage(): ?string { return $this->message; }
    public function getIcon(): ?string { return $this->icon; }
    public function getColor(): ?string { return $this->color; }
    public function isRead(): ?bool { return $this->is_read; }
    public function getCreatedAt(): ?string { return $this->created_at; }

    // Setters
    public function setIdNotification(?int $id_notification): void { $this->id_notification = $id_notification; }
    public function setIdUser(?int $id_user): void { $this->id_user = $id_user; }
    public function setType(?string $type): void { $this->type = $type; }
    public function setTitle(?string $title): void { $this->title = $title; }
    public function setMessage(?string $message): void { $this->message = $message; }
    public function setIcon(?string $icon): void { $this->icon = $icon; }
    public function setColor(?string $color): void { $this->color = $color; }
    public function setIsRead(?bool $is_read): void { $this->is_read = $is_read; }
    public function setCreatedAt(?string $created_at): void { $this->created_at = $created_at; }
}
?>