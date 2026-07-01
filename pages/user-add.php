<?php
include('../includes/header.php');
?>

<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold">Add User</h2>
        <hr>
    </div>
</div>

<div id="response" class="alert alert-success alert-dismissible fade show" role="alert" style="display:none;">
    <div class="message"></div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
                        
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white">
                <h5 class="mb-0">User Information</h5>
            </div>
            <div class="card-body">
                <form method="post" id="add_user">
                    <input type="hidden" name="action" value="add_user">

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <input type="text" class="form-control required" name="name" placeholder="Full Name">
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control required" name="username" placeholder="Enter username">
                        </div>
                        <div class="col-md-4">
                            <input type="email" class="form-control required" name="email" placeholder="Enter user's email address">
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="phone" placeholder="Enter user's phone number">
                        </div>
                        <div class="col-md-4">
                            <input type="password" class="form-control required" name="password" id="password" placeholder="Enter user's password">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 text-end">
                            <button type="submit" id="action_add_user" class="btn btn-success" data-loading-text="Adding...">
                                <i class="bi bi-person-plus-fill me-1"></i> Add User
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
include('../includes/footer.php');
?>
