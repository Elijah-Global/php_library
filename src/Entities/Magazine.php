<?php

namespace LibrarySystem\Entities;
use InvalidArgumentException;
use LibrarySystem\Entities\Borrowable;
use LibrarySystem\Entities\LibraryItem;

class Magazine extends LibraryItem implements Borrowable {
    private int $issueNumber;
    private int $availableCopies;
    
    public function __construct(string $title, string $author, string $id, int $issueNumber, int $availableCopies) {
        parent::__construct($title, $author, $id);
        if ($availableCopies < 0) {
            throw new InvalidArgumentException("Available copies cannot be negative");
        }
        $this->issueNumber = $issueNumber;
        $this->availableCopies = $availableCopies;
    }
    
    public function getDetails(): string {
        return "Magazine: {$this->title} by {$this->author}, Issue: {$this->issueNumber}, ID: {$this->id}, Available: {$this->availableCopies}";
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