$(document).ready(function () {
    $('.password_show').click(function () {
        var cur_type = $(this).siblings('input').attr('type')
        if (cur_type == 'password') {
            $(this).html("<i class='fa fa-eye'></i>")
            $(this).siblings('input').attr('type', 'text').focus()
        } else {
            $(this).html("<i class='fa fa-eye-slash'></i>")
            $(this).siblings('input').attr('type', 'password').focus()
        }
    })
    $('#formModal').on("show.bs.modal", function () {
        $(this).find(".password_show").html("<i class='fa fa-eye-slash'></i>")
        $(this).find(".password_show").siblings('input').attr('type', 'password')
    })
    $('#formModal').on("hide.bs.modal", function () {

        $('#password-form')[0].reset();
        $('#password-form [name="id"]').val('')
    })

    $('#add_pass').click(function () {
        $('#FormModalTitle').text("Add New Password Record")
        $('#formModal').modal('show')
    })
    $('.encrypted_pass').on('mouseenter', function () {
        var _this = $(this)
        var encrypted_pass = _this.attr('data-value')
        var dotted_pass = _this.text()
        $.ajax({
            url: 'api.php?action=get_real_password',
            method: 'post',
            data: {
                password_encrypted: encrypted_pass
            },
            dataType: 'json',
            error: err => {
                console.error(err)
            },
            success: function (response) {
                if (response.status == 'success') {
                    _this.text(response.password_decrypt)
                } else {
                    console.error(err)
                }
            }
        })
        _this.on('mouseleave', function () {
            _this.text(dotted_pass)
        })
    })

    $('.view_record').click(function () {
        var id = $(this).attr('data-id');
        console.log("Clicked View button with ID: " + id);

        $.ajax({
            url: 'api.php?action=view_record',
            method: 'post',
            data: {
                id: id
            },
            dataType: 'json',
            error: function (err) {
                console.error(err);
            },
            success: function (response) {
                console.log("Response received: ", response);

                if (response.status === 'success') {
                    populateViewModal(response.data);
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                    });
                    Toast.fire({
                        icon: 'success',
                        title: 'View Credentials'
                    });
                } else {
                    console.error(response.error);
                    alert(response.error);
                }
            }
        });
    });
    function populateViewModal(data) {
        $('#viewModal #ViewModalTitle').text("View Password Record");
        $('#view-password-form [name="id"]').val(data.id);
        $('#view-name').val(data.name);
        $('#view-email').val(data.email);
        $('#view-username').val(data.username);
        $('#view-password').val(data.password);
        $('#view-description').val(data.description);
        $('#view-site').val(data.site);
        $('#viewModal').modal('show');
    }

    $('.edit_record').click(function () {
        var id = $(this).attr('data-id');
    
        $.ajax({
            url: 'api.php?action=get_single&id=' + id,
            dataType: "json",
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('AJAX Error:', textStatus, errorThrown);
            },
            success: function (response) {
                if (response.status === 'success') {
                    $("[name='id']").val(response.data.id);
                    $("[name='name']").val(response.data.name);
                    $("[name='email']").val(response.data.email);
                    $("[name='username']").val(response.data.username);
                    $("[name='password']").val(response.data.password);
                    $("[name='description']").val(response.data.description);
                    $("[name='site']").val(response.data.site);
                    $('#FormModalTitle').text("Edit Password Record");
                    $('#formModal').modal('show');
                } else if (response.status === 'failed') {
                    console.error('Request Failed:', response.error);
                    alert(response.error);
                } else {
                    console.error('Unknown Error:', 'Editing Data Failed due to an unknown reason.');
                    alert('Editing Data Failed due to an unknown reason.');
                }
            }
        });
    });
    
    $('#password-form').submit(function (e) {
        e.preventDefault();
        $('button[form="password-form"]').attr('disabled', true);
        var el = $("<div>");
        el.addClass("alert msg");
        el.hide();
        $('.msg').remove();
    
        $.ajax({
            url: "api.php?action=save",
            type: "post",
            data: $(this).serialize(),
            dataType: 'json',
            error: function (err) {
                console.error(err);
            },
            success: function (response) {
                if (response.status === 'success') {
                    // Show the success notification using SweetAlert2
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Credentials saved successfully',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        location.replace("index.php");
                    });
                } else if (response.status === 'failed') {
                    // Handle failures
                    el.addClass("alert-danger");
                    el.text(response.error);
                } else {
                    // Handle unknown errors
                    el.addClass("alert-danger");
                    el.text("Saving Data Failed due to an unknown reason.");
                }
                $('#password-form').prepend(el);
                el.show('slideDown');
                $('button[form="password-form"]').attr('disabled', false);
            }
        })
    })
})