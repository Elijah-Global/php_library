<?php

namespace LibrarySystem\Entities;
use InvalidArgumentException;
use LibrarySystem\Entities\Borrowable;
use LibrarySystem\Entities\LibraryItem;

class Book extends LibraryItem implements Borrowable {
    private int $availableCopies;
    
    public function __construct(string $title, string $author, string $isbn, int $availableCopies) {
        parent::__construct($title, $author, $isbn);
        if ($availableCopies < 0) {
            throw new InvalidArgumentException("Available copies cannot be negative");
        }
        $this->availableCopies = $availableCopies;
    }
    
    public function getDetails(): string {
        return "Book: {$this->title} by {$this->author}, ISBN: {$this->id}, Available: {$this->availableCopies}";
    }
    
    public function borrowItem(): bool {
        if ($this->availableCopies > 0) {
            $this->availableCopies--;
            return true;
        }
        return false;
    }
    
    public function returnItem(): bool {
        $this->availableCopies++;
        return true;
    }
    
    public function getAvailableCopies(): int {
        return $this->availableCopies;
    }
}