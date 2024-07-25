<?php
require_once "config.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP_CRUD</title>
    <link rel="stylesheet" href="./assets/all.min.css">
    <link rel="stylesheet" href="./assets/bootstrap.min.css">
    <!-- DataTables and Responsive DataTables CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <script src="./assets/jquery-3.6.1.min.js"></script>
    <script src="./assets/all.min.js"></script>
    <script src="./assets/bootstrap.bundle.min.js"></script>
    <!-- DataTables JavaScript -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@10'></script>
</head>
<style>
    th.center-text,
    td.center-text {
        text-align: center;
    }

    #example td {
        text-align: center;
    }

    .truncate-text {
        max-width: 100px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    table#example th.center-text:last-child,
    table#example td.center-text:last-child {
        text-align: center;
        min-width: 100px;
    }

    #add_pass {
        display: block;
        margin: 0 auto;
        margin-top: 10px;
    }

    .dropdown-menu .dropdown-item {
        text-align: center;
    }

    .dropdown-menu .dropdown-item {
        padding: 5px;
    }
</style>

<body>
    <nav class="navbar navbar-dark navbar-expand-lg bg-dark bg-gradient">
        <div class="container">
            <a class="navbar-brand" href="./">Simple PHP CRUD</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link">
                            <button class="btn btn-info" onclick="window.location.href='./'">Home</button>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link">
                            <button class="btn btn-danger" id="logoutBtn">Logout</button>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <div class="col-lg-12 col-md-12 col-sm-12 mx-auto">
            <?php if (!empty($successMsg)) : ?>
                <div class="alert alert-success rounded-0 mb-2">
                    <div><?= $successMsg ?></div>
                </div>
            <?php endif; ?>
            <div class="card rounded-0 shadow">
                <div class="card-header rounded-0">
                    <div class="d-flex">
                        <div class="col-auto flex-shrink-1 flex-grow-1">

                        </div>
                        <div class="col-auto">
                            <button class="btn btn-primary" id="add_pass"><i class="fa fa-plus"></i> Add Data</button>
                        </div>
                    </div>
                </div>
                <div class="card-body rounded-0">

                    <div class="container-fluid">
                        <table id="example" class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th class="center-text text-center">Name</th>
                                    <th class="center-text text-center">Email</th>
                                    <th class="center-text text-center">Username</th>
                                    <th class="center-text text-center">Password</th>
                                    <th class="center-text text-center">Description</th>
                                    <th class="center-text text-center">Site</th>
                                    <th class="center-text text-center">Task</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Function to display partial passwords with limited asterisks
                                function displayPartialPassword($password, $displayLength = 6)
                                {
                                    $passwordLength = strlen($password);
                                    $displayPassword = str_repeat("*", min($displayLength, $passwordLength));
                                    echo $displayPassword;
                                }

                                $data = $db->get_results("SELECT * FROM `record_list` ORDER BY date_added DESC");
                                if (count($data) > 0) {
                                    foreach ($data as $row) {
                                        echo "<tr>";
                                        echo "<td class=\"p-1 center-text truncate-text\"><div class=\"lh-1\">{$row['name']}</div></td>";
                                        echo "<td class=\"p-1 center-text truncate-text\">{$row['email']}</td>";
                                        echo "<td class=\"p-1 center-text truncate-text\">{$row['username']}</td>";
                                        echo "<td class=\"p-1 center-text truncate-text fw-bold encrypted_pass\" data-value=\"{$row['password']}\">";
                                        displayPartialPassword($row['password'], 6);
                                        echo "</td>";
                                        echo "<td class=\"p-1 center-text truncate-text\">{$row['description']}</td>";
                                        echo "<td class=\"p-1 center-text truncate-text\">{$row['site']}</td>";
                                        echo "<td class=\"text-center center-text\">
                <button class=\"btn btn-warning border view_record\" data-id=\"{$row['id']}\">View</button>
                <button class=\"btn btn-secondary edit_record\" data-id=\"{$row['id']}\">Edit</button>
                <button class=\"btn btn-danger\" onclick=\"confirmDelete({$row['id']})\">Delete</button>
            </td></tr>";
                                    }
                                }
                                ?>
                            </tbody>


                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- View Modal -->
    <div class="modal fade" id="viewModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog rounded-0 modal-dialog-centered modal-lg modal-dialog-scrollable">
            <div class="modal-content rounded-0">
                <div class="modal-header rounded-0">
                    <h1 class="modal-title fs-5" id="ViewModalTitle">View Password Record</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body rounded-0">
                    <div class="container-fluid">
                        <form action="" id="view-password-form" method="POST">
                            <input type="hidden" name="id" value="">
                            <!-- Fields to display the record details -->
                            <div class="mb-3">
                                <label for="view-name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="view-name" name="view-name" disabled>
                            </div>
                            <div class="mb-3">
                                <label for="view-email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="view-email" name="view-email" disabled>
                            </div>
                            <div class="mb-3">
                                <label for="view-username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="view-username" name="view-username" disabled>
                            </div>
                            <div class="mb-3">
                                <label for="view-password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="view-password" name="view-password" disabled>
                            </div>
                            <div class="mb-3">
                                <label for="view-description" class="form-label">Description</label>
                                <textarea class="form-control" id="view-description" name="view-description" rows="3" disabled></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="view-site" class="form-label">Site</label>
                                <input type="text" class="form-control" id="view-site" name="view-site" disabled>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Edit Modal -->
    <div class="modal fade" id="formModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog rounded-0 modal-dialog-centered modal-lg modal-dialog-scrollable">
            <div class="modal-content rounded-0">
                <div class="modal-header rounded-0">
                    <h1 class="modal-title fs-5" id="FormModalTitle"></h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body rounded-0">
                    <div class="container-fluid">
                        <form action="" id="password-form" method="POST">
                            <input type="hidden" name="id" value="">
                            <div class="mb-3">
                                <label for="name" class="control-label">Name</label>
                                <input type="text" class="form-control rounded-0" id="name" name="name" value="" required="required">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="control-label">Email</label>
                                <input type="email" class="form-control rounded-0" id="email" name="email" value="" required="required">
                            </div>

                            <div class="mb-3">
                                <label for "username" class="control-label">Username</label>
                                <input type="text" class="form-control rounded-0" id="username" name="username" value="" required="required">
                            </div>
                            <div class="mb-3">
                                <label for="password" class="control-label">Password</label>
                                <div class="input-group rounded-0">
                                    <input type="password" class="form-control rounded-0" id="password" name="password" value="" required="required">
                                    <button class="input-group-button btn btn-light border password_show" tabindex="-1" type="button"><i class="fa fa-eye-slash"></i></button>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="control-label">Description</label>
                                <textarea name="description" id="description" cols="30" rows="3" class="form-control rounded-0"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="site" class="control-label">Site</label>
                                <input type="text" class="form-control rounded-0" id="site" name="site" value="" required="required">
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" form="password-form">Save</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('logoutBtn').addEventListener('click', function(event) {
            event.preventDefault(); // Prevents the default action of the link

            // Show SweetAlert for successful logout
            Swal.fire({
                icon: 'success',
                title: 'Logged out successfully!',
                text: 'You have been logged out.',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                window.location.href = "login.php"; // Redirect after showing the SweetAlert
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            var alertShown = localStorage.getItem('alertShown');

            if (alertShown !== 'true') {
                Swal.fire({
                    icon: 'info',
                    title: 'Important Notice!',
                    html: 'This page contains sensitive data. Please handle the information with care. Once you have finished working with the data, kindly log out to ensure the security of the information.',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Got it!',
                    cancelButtonText: 'Logout',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        localStorage.setItem('alertShown', 'true');
                        // Handle confirmation action if needed
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        window.location.href = 'login.php?logout=now'; // Redirect to logout on cancel (optional)
                    }
                });
            }
        });

        // Example of a delete action confirmation using SweetAlert2
        function confirmDelete(recordId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This action cannot be undone. Do you want to proceed with the deletion?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it',
                cancelButtonText: 'No, cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Deleted!',
                        text: 'Your file is being deleted...',
                        icon: 'success',
                        showConfirmButton: false
                    });

                    // Perform the delete action here or redirect to the deletion URL
                    setTimeout(() => {
                        window.location.href = `api.php?action=delete_record&id=${recordId}`;
                    }, 1000); // Delay of 1 second (1000 milliseconds)
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire(
                        'Cancelled',
                        'Your data is safe :)',
                        'error'
                    );
                }
            });
        }


        $(document).ready(function() {
            $('#example').DataTable({
                responsive: true,
                "order": [
                    // [0, "desc"]
                ]
            });
        });
    </script>
</body>

<script src='./assets/crud.js'></script>

</html>