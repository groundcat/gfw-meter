<?php

include 'templates/header.php';

$comments = app('comment')->getComments();
// Id of currently authenticated user
$userId = ASSession::get('user_id');

// Load the functions of WM
include('../functions.php');

// Insert the new domain to the database from form submission
if (isset($_POST['domain']) && !isset($_POST['enabled'])) {
    $domain = $_POST['domain'];
    // Validate the domain
    if (!preg_match('/^(?!\-)(?:(?:[a-zA-Z\d][a-zA-Z\d\-]{0,61})?[a-zA-Z\d]\.){1,126}(?!\d+)[a-zA-Z\d]{1,63}$/', $domain) || !filter_var($domain, FILTER_VALIDATE_DOMAIN)) {
        echo '{"error":"Invalid domain","code":400}';
        exit;
    }
    // Validate if the domain is accessible
    if (checkdnsrr($domain, "A") === false) {
        echo '{"error":"Domain not resolved","code":400}';
        exit;
    }

    // Insert domain to database
    app('db')->insert('wm_domains', array(
        "domain" => $domain,
        "user_id" => $_SESSION['user_id'],
        "enabled" => 1
    ));
    // Print a success message box
    echo '<div class="alert alert-success">Domain added successfully</div>';
}

// Update the domain status from the form submission
if (isset($_POST['domain_id']) && isset($_POST['enabled'])) {
    $domain_id = $_POST['domain_id'];
    $enabled = $_POST['enabled'];
    // Validate if both domain_id and enabled are integers
    if (!is_numeric($domain_id) || !is_numeric($enabled)) {
        echo '{"error":"Invalid domain_id or enabled","code":400}';
        exit;
    }
    // Update domain status where domain_id and user_id match
    app('db')->update('wm_domains', array(
        "enabled" => $enabled
    ), 
    "domain_id = $domain_id AND user_id = $userId");

    // Print a success message box
    echo '<div class="alert alert-success">Domain status updated successfully</div>';
}






?>
        
<div class="row">
    <?php
        // Include sidebar template
        // and set active page to "home".
        $sidebarActive = 'home';
        require 'templates/sidebar.php';
    ?>

    <div class="col-md-9 col-lg-10">

        <h3 class="mb-4 page-header">
            My Domains
        </h3>

        <!-- start: Monitors List -->
        <?php
        // Get all monitors for currently authenticated user
        $domains = app('db')->select(
            "SELECT * FROM `wm_domains` WHERE `user_id` = $userId ORDER BY `latest_test_time` DESC"
        );

        ?>
        <div class="mt-5">
            <table class="table table-striped w-100" >
                <thead>
                    <tr>
                        <th>Domain</th>
                        <th>Creation Time</th>
                        <th>Latest Report</th>
                        <th>Latest Score %</th>
                        <th>Monitoring</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <?php foreach ($domains as $domain) : ?>
                    <tr class="user-row">
                        <td><?= e($domain['domain']) ?></td>
                        <td><?= e($domain['created_time']) ?></td>
                        <td>
                            <?php if ($domain['latest_test_id']) : ?>
                            <a href="../test_data.php?id=<?= $domain['latest_test_id'] ?>" target="_blank">
                                <?= e($domain['latest_test_time']) ?>
                            </a>
                            <?php else: ?>
                                <span class="text-muted">Queued</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($domain['latest_score'] == 0) : ?>
                                <span class="badge badge-success"><i class="fas fa-smile"></i> <?=$domain['latest_score']?>%</span>
                            <?php elseif ($domain['latest_score'] && $domain['latest_score'] > 0): ?>
                                <span class="badge badge-danger"><i class="fas fa-frown"></i> <?=$domain['latest_score']?>%</span>
                            <?php else: ?>
                                <span class="badge badge-secondary">Queued</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($domain['enabled']) : ?>
                                <span class="badge badge-success">Enabled</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Disabled</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <!-- Form that changes status -->
                            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                                <input type="hidden" name="domain_id" value="<?= $domain['domain_id'] ?>" required>
                                <input type="hidden" name="enabled" value="<?= $domain['enabled'] ? '0' : '1' ?>" required>
                                <input type="hidden" name="<?= ASCsrf::getTokenName() ?>" value="<?= ASCsrf::getToken() ?>">
                                <button type="submit" class="btn btn-sm btn-outline-primary">
                                    <?= $domain['enabled'] ? 'Disable' : 'Enable' ?>
                                </button>
                            </form>

                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <!-- end: Monitors List -->

        <br>
                                
        <!-- start: Add domains form -->
        <div class="card">
            <div class="card-header">
                Add a domain
            </div>
            <div class="card-body">
                <!-- Form submit POST to self -->
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                    <!-- Form input-->
                    <div class="form-group">
                        <label>
                            Domain
                            <small>Input a domain name, e.g. example.com</small>
                        </label>
                        <input name="domain" class="form-control" placeholder="example.com" required>

                        <input type="hidden" name="<?= ASCsrf::getTokenName() ?>" value="<?= ASCsrf::getToken() ?>">

                    </div>
                    <button type="submit" class="btn btn-primary">
                        Add
                    </button>
                </form>
            </div>
        </div>
        <!-- end: Add domains form -->

        <br>

        
    </div>
</div>

    <?php include 'templates/footer.php'; ?>
    <script src="assets/js/app/index.js"></script>
  </body>
</html>
