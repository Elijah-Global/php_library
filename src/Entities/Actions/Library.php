<?php

namespace LibrarySystem\Entities\Actions;

use LibrarySystem\Entities\LibraryItem;
use LibrarySystem\Entities\Member;
use LibrarySystem\Entities\Actions\Borrowing;

class Library implements \Iterator {
    private array $items;
    private array $members;
    private array $borrowings;
    private int $position = 0;
    private static int $totalItems = 0;
    
    public function __construct() {
        $this->items = [];
        $this->members = [];
        $this->borrowings = [];
    }
    
    public function addItem(LibraryItem $item): void {
        $this->items[$item->getId()] = $item;
        self::$totalItems++;
    }
    
    public function removeItem(string $id): bool {
        if (isset($this->items[$id])) {
            unset($this->items[$id]);
            self::$totalItems--;
            return true;
        }
        return false;
    }
    
    public function addMember(Member $member): void {
        $this->members[$member->getMemberId()] = $member;
    }
    
    public function removeMember(string $memberId): bool {
        if (isset($this->members[$memberId])) {
            if (empty($this->members[$memberId]->getBorrowedBooks())) {
                unset($this->members[$memberId]);
                return true;
            }
            throw new \Exception("Cannot remove member with active borrowings");
        }
        return false;
    }
    
    public function borrowItem(string $itemId, string $memberId): bool {
        if (!isset($this->items[$itemId]) || !isset($this->members[$memberId])) {
            return false;
        }
        
        $item = $this->items[$itemId];
        $member = $this->members[$memberId];
        
        if ($item->borrowItem()) {
            $borrowing = new Borrowing($item, $member);
            $this->borrowings[] = $borrowing;
            $member->addBorrowedBook($borrowing);
            return true;
        }
        return false;
    }
    
    public function returnItem(Borrowing $borrowing): bool {
        if ($borrowing->returnBook()) {
            $borrowing->getMember()->removeBorrowedBook($borrowing);
            // Reindex the array after filtering to avoid gaps in numeric keys
            $this->borrowings = array_values(array_filter($this->borrowings, fn($b) => $b !== $borrowing));
            return true;
        }
        return false;
    }
    
    public function getBorrowingReport(): string {
        $report = "Borrowing Report:\n";
        foreach ($this->borrowings as $borrowing) {
            $item = $borrowing->getItem();
            $member = $borrowing->getMember();
            $dueDate = $borrowing->getDueDate()->format('Y-m-d');
            $status = $borrowing->getReturnDate() ? "Returned" : "Borrowed";
            $report .= "{$item->getDetails()}, Borrowed by: {$member->getName()}, Due: {$dueDate}, Status: {$status}\n";
        }
        return $report;
    }
    
    public static function getTotalItems(): int {
        return self::$totalItems;
    }
    
    public function getAllItems(): array {
        return $this->items;
    }
    
    public function getBorrowings(): array {
        return $this->borrowings;
    }
    
    // Iterator interface methods
    public function rewind(): void {
        $this->position = 0;
    }
    
    public function current(): mixed {
        $keys = array_keys($this->items);
        if (!isset($keys[$this->position])) {
            return false;
        }
        return $this->items[$keys[$this->position]];
    }
    
    public function key(): mixed {
        $keys = array_keys($this->items);
        return $keys[$this->position] ?? null;
    }
    
    public function next(): void {
        $this->position++;
    }
    
    public function valid(): bool {
        $keys = array_keys($this->items);
        return isset($keys[$this->position]);
    }
}