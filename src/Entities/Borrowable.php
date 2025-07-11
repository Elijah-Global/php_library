<?php

namespace LibrarySystem\Entities;

interface Borrowable {
    public function borrowItem(): bool;
    public function returnItem(): bool;
}
?>