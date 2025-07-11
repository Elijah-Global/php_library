# Library Management System

A PHP-based Object-Oriented Programming (OOP) project implementing a Library Management System to manage books, magazines, members, and borrowing activities, incorporating namespaces, iterables, and static properties.

## Features
- Manages books and magazines with properties like title, author, and available copies
- Handles member registration with email validation
- Tracks borrowing and returning activities with due dates
- Generates borrowing reports
- Implements OOP principles: inheritance, encapsulation, polymorphism, abstraction
- Uses namespaces for code organization
- Implements Iterator interface for item iteration
- Uses static properties to track total items
- Includes input validation for negative copies and invalid emails

## Requirements
- PHP 7.4 or higher
- Web server (e.g., Apache) or PHP's built-in server

## Installation
1. Clone or download this repository
2. Place the files in your web server's root directory
3. Ensure the `src` directory is in the same directory as `index.php`

## Usage
1. Run the PHP server:

2. Access `http://localhost:8000/index.php` in your browser or run:

3. The script will demonstrate:
- Adding 3 books and 3 magazines
- Registering 3 members
- Borrowing and returning operations
- Generating borrowing reports
- Iterating over library items
- Displaying total item count

## Project Structure
- `src/Entities/`:
- `Borrowable.php`: Interface for borrowable items
- `LibraryItem.php`: Abstract base class
- `Book.php`: Book class
- `Magazine.php`: Magazine class
- `Member.php`: Member class
- `src/Actions/`:
- `Borrowing.php`: Borrowing class
- `Library.php`: Main library management class
- `index.php`: Demonstration script
- `README.md`: This file

## OOP Implementation Details
### Namespaces (LibrarySystem\Entities and LibrarySystem\Actions)
Namespaces organize code to prevent naming conflicts and improve maintainability. In this project, entity classes (`Book`, `Magazine`, `Member`, `LibraryItem`, `Borrowable`) are under `LibrarySystem\Entities`, grouping core data models. Action-related classes (`Borrowing`, `Library`) are in `LibrarySystem\Actions`, separating operational logic. This structure enhances modularity, making it easier to scale the system or integrate with other libraries. Namespaces are used in `index.php` with `use` statements to access classes, ensuring clear referencing and avoiding global namespace pollution. For example, `LibrarySystem\Entities\Book` uniquely identifies the `Book` class, allowing multiple projects to coexist without conflicts.

### Iterator Interface
The `Library` class implements PHP's `Iterator` interface, enabling `foreach` iteration over the items collection. The interface requires methods like `rewind()`, `current()`, `key()`, `next()`, and `valid()`, which manage iteration state. This allows users to loop through library items seamlessly, as shown in `index.php`. The Iterator interface is useful because it provides a standardized way to traverse collections, hiding internal array details and improving code readability. It makes the `Library` class behave like a native PHP array, enhancing usability for developers accessing library items.

### Static Properties
The `Library` class includes a static property `$totalItems` to track the total number of items (books and magazines). Unlike instance properties, static properties belong to the class itself, shared across all instances. The static method `getTotalItems()` accesses this count. In `index.php`, this is demonstrated by displaying the total after adding items. Static properties differ from instance properties as they maintain a single value for the class, not per object, making them ideal for tracking global states like total inventory without needing a specific `Library` instance.

## Notes
- Data is stored in memory using arrays
- DateTime handles borrowing and due dates
- Error handling is implemented throughout