# Taskify

Taskify is a simple yet powerful To-Do application built with Laravel. It allows users to manage their tasks by creating
task groups, adding tasks, and collaborating with others. With role-based access control (RBAC) and email notifications,
Taskify provides a robust solution for individual and team productivity.

## Screenshots

![Task Group Page](https://i.imgur.com/bkHqT3b.png)

![Show Task Group](https://i.imgur.com/OMyTAc9.png)

![List Roles Page](https://i.imgur.com/9X2ipu7.png)

## Features

### 1. Task Groups

- **Create Task Groups:** Organise your tasks by grouping them into categories or projects.
- **Manage Tasks:** Add, edit, or delete tasks within each group to keep track of your progress.

### 2. Role-Based Access Control (RBAC)

- **User Roles:** Assign specific roles to users within a task group, such as Admin, Editor, or Viewer.
- **Custom Permissions:** Control who can create, edit, or delete tasks, and manage the group settings.

### 3. Collaboration

- **Invite Users:** Collaborate with others by inviting them to join your task group.
- **Role Management:** Change user roles at any time to adjust their permissions.
- **Notifications:** Users receive email notifications when invited to a task group, or when their role changes.

## Installation

To get started with Taskify, follow the instructions below.

### Prerequisites

- PHP 8.x
- Composer
- MySQL or any other supported database
- Laravel 9.x

### Steps

1. **Clone the Repository**
    ```bash
    git clone https://github.com/tbhaxor/Taskify.git
    cd Taskify
    ```

2. **Install Dependencies**
    ```bash
    composer install
    npm install
    npm run dev
    ```

3. **Set Up Environment**
    - Duplicate the `.env.example` file and rename it to `.env`.
    - Update the `.env` file with your database and mail server settings.
    - For authentication, we are using zitadel. Please check [this link](https://socialiteproviders.com/Zitadel/) to
      configure it.

4. **Generate Application Key**
    ```bash
    php artisan key:generate
    ```

5. **Run Migrations**
    ```bash
    php artisan migrate
    ```

6. **Start the Development Server**
    ```bash
    php artisan serve
    ```

## Usage

Once installed, you can start using Taskify by accessing it in your web browser.

### Creating a Task Group

1. Navigate to the "Task Groups" section.
2. Click on "Create New Group" and provide a name for your group.
3. Once created, you can start adding tasks to your group.

### Managing Tasks

- **Add Task:** Within a task group, click on "Add New Task" to create a task.
- **Edit Task:** Click on a task to edit its details.
- **Delete Task:** Remove a task by clicking the delete option next to it.
- **Update Task** Click on the edit task button to update its title, status and description of it.

> ![NOTE]
> If the status is set to completed, you can't change it to in-progress or any other status. Instead, you should create
> a new task.

### Inviting Users

1. Go to the task group where you want to add collaborators.
2. Click on "Show User Invite" and enter their email address.
3. Assign them a role (Admin, Editor, Viewer) and send the invitation.
4. The user will receive an email invitation to join the group.

### Changing User Roles

1. In the task group, go to the "Show Group Sharing" section.
2. Click on the user's current role and select a new role from the dropdown.

## Contributing

Contributions are welcome! To contribute:

1. Fork the repository.
2. Create a new branch (`git checkout -b your branch`).
3. Make your changes.
4. Commit your changes (`git commit -m 'Add some feature'`).
5. Push to the branch (`git push origin feature-branch`).
6. Create a pull request.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

## Contact

For any questions or suggestions, feel free to open an issue or contact the repository owner at [your email address].

---

**Happy Tasking! 🖖**
