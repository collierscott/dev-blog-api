<?php


namespace App\Entity;


interface PublishedDateEntityInterface
{
    public function setPublishedAt(\DateTimeInterface $publishedAt): PublishedDateEntityInterface;
    public function getPublishedAt(): ?\DateTimeInterface;
}