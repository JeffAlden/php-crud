<?php
require_once "config.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Secure Data Manager</title>
    <link rel="stylesheet" href="./assets/all.min.css">
    <link rel="stylesheet" href="./assets/bootstrap.min.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11">
    <script src="./assets/jquery-3.6.1.min.js"></script>
    <script src="./assets/all.min.js"></script>
    <script src="./assets/bootstrap.bundle.min.js"></script>
    <style>
        body {
            margin: 0;
            padding: 0;
            background: url('./assets/pexels-pixabay-36717.jpg') no-repeat center center fixed;
            background-size: cover;
        }

        /* Styling for the Glassmorphism effect */
        .glassmorphic-card {
            /* background: rgba(255, 255, 255, 0.2); */
            background: transparent;
            border-radius: 15px;
            /* backdrop-filter: blur(10px); */
            border: 1px solid rgba(255, 255, 255, 0.18);
        }

        .card-body {
            background: transparent;
            /* Making the body transparent for the effect */
        }

        /* Additional styling to fine-tune the design */
        .card-footer {
            background: rgba(255, 255, 255, 0.2);
            border-bottom-left-radius: 15px;
            border-bottom-right-radius: 15px;
        }

        .btn {
            border-radius: 10px;
        }



        /* Clock CSS */
        .digital-clock {
            position: absolute;
            bottom: 20px;
            right: 20px;
            color: white;
            font-family: 'BebasNeueRegular', Arial, Helvetica, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .digital-clock #Date {
            font-size: 20px;
            margin-bottom: 3px;
        }

        .digital-clock ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
        }

        .digital-clock ul li {
            font-size: 2em;
        }

        .digital-clock #point {
            margin: 0 5px;
        }
    </style>
</head>

<!-- <body class="bg-gradient bg-dark"> -->

<body>
    <div class="container my-5">
        <div class="col-lg-8 col-md-7 col-sm-12 mx-auto my-auto">
            <h1 class="text-center text-light fw-bold">PHP Simple CRUD Login</h1>
            <hr class="bg-light">
        </div>
        <div class="col-lg-5 col-md-7 col-sm-12 mx-auto my-auto">
            <!-- PHP Session Message
            <?php if (isset($_SESSION['success_message'])) : ?>
                <div class="alert alert-success rounded-0 mb-2">
                    <div><?= $_SESSION['success_message'] ?></div>
                </div>
                <?php unset($_SESSION['success_message']) ?>
            <?php endif; ?> -->

            <div class="card glassmorphic-card my-4">
                <div class="card-body">
                    <div class="container-fluid">
                        <form action="" id="login-form" method="POST">
                            <div class="mb-3">
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password here" value="" required="required">
                                    <button class="btn btn-light border input-group-button password_show" type="button"><i class="fa fa-eye-slash"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card-footer py-2">
                    <div class="d-flex justify-content-center">
                        <div class="col-lg-4 col-md-6 col-sm-12">
                            <button class="btn btn-primary w-100" form="login-form"><i class="fa fa-save"></i> Login</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    </div>

    <!-- Clock container -->
    <div class="digital-clock">
        <div id="Date"></div>
        <ul>
            <li id="hours"></li>
            <li id="point">:</li>
            <li id="min"></li>
            <li id="point">:</li>
            <li id="sec"></li>
        </ul>
    </div>

</body>
<!-- jQuery -->
<script src="./assets/js/jquery-3.6.1.min.js"></script>
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Clock related JavaScript
    $(document).ready(function() {
    var monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    var dayNames = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];

    updateTime(); // Invoke the function immediately

    function updateTime() {
        var time = new Date();

        // Display date
        var dateFormatted = dayNames[time.getDay()] + " " + time.getDate() + ' ' + monthNames[time.getMonth()] + ' ' + time.getFullYear();
        $('#Date').html(dateFormatted);

        // Display time
        var hours = time.getHours();
        var minutes = time.getMinutes();
        var seconds = time.getSeconds();

        $("#hours").text((hours < 10 ? "0" : "") + hours);
        $("#min").text((minutes < 10 ? "0" : "") + minutes);
        $("#sec").text((seconds < 10 ? "0" : "") + seconds);
    }

    setInterval(updateTime, 1000); // Update the time every second
});

    // Test SweetAlert2 functionality
    // document.addEventListener('DOMContentLoaded', function () {
    //         This script will execute when the page is loaded
    //         Swal.fire({
    //             icon: 'info',
    //             title: 'SweetAlert Test',
    //             text: 'This is a test to check if SweetAlert works on this page.'
    //         });
    //     });

    $(document).ready(function() {
        $('.password_show').click(function() {
            var cur_type = $(this).siblings('input').attr('type');
            if (cur_type == 'password') {
                $(this).html("<i class='fa fa-eye'></i>");
                $(this).siblings('input').attr('type', 'text').focus();
            } else {
                $(this).html("<i class='fa fa-eye-slash'></i>");
                $(this).siblings('input').attr('type', 'password').focus();
            }
        });

        $('#login-form').submit(function(e) {
            e.preventDefault();
            $('button[form="login-form"]').attr('disabled', true);
            $.ajax({
                url: "api.php?action=login",
                type: "post",
                data: $(this).serialize(),
                dataType: 'json',
                error: function(err) {
                    console.error(err);
                    // SweetAlert for error
                    Swal.fire({
                        icon: 'error',
                        title: 'An error occurred',
                        text: 'Please try again.'
                    });
                    $('button[form="login-form"]').attr('disabled', false);
                },
                success: function(response) {
                    if (response.status == 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Login Successful!',
                            showConfirmButton: false,
                            timer: 900 // 1 second delay for the alert
                        }).then(() => {
                            setTimeout(function() {
                                window.location.href = 'index.php';
                            }, 500); // 1 second delay before redirection
                        });
                    } else if (response.status == 'failed') {
                        // SweetAlert for failed login with error message
                        Swal.fire({
                            icon: 'error',
                            title: 'Login failed',
                            text: response.error
                        });
                    } else {
                        // SweetAlert for other failures
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Saving Data Failed due to unknown reasons.'
                        });
                    }
                    $('button[form="login-form"]').attr('disabled', false);
                }
            });
        });
    });
</script>

</html>