<?php

// Take domain GET request
$domain = $_POST['domain'] ?? null;
$shareable = $_POST['shareable'] ?? null;

// Load the functions
require_once dirname(__FILE__) . '/user/ASEngine/AS.php';
require_once 'functions.php';
require_once 'config.php';

if (isset($domain)) {

    // Validate if $domain is a valid domain name
    if (!preg_match('/^(?!\-)(?:(?:[a-zA-Z\d][a-zA-Z\d\-]{0,61})?[a-zA-Z\d]\.){1,126}(?!\d+)[a-zA-Z\d]{1,63}$/', $domain) || !filter_var($domain, FILTER_VALIDATE_DOMAIN)) {
      echo '{"error":"Invalid domain","code":400}';
      exit;
    }

    // Get current timestamp
    $timestamp = time();

    // Send request to the API endpoint
    $api_json = api_v1($domain);

    // Send request to the pastebin API endpoint
    if ($shareable == 'yes') {
        $api_paste_name = $domain . '_' . $timestamp;
        $pastebin_url = pastebin($api_json, $api_paste_name);
    } else {
        $pastebin_url = '';
    }

    // Record the result into the database

      // Get the result
      $test_result = $api_json;

      // Get the percentage_blocking_score value from the test result JSON
      $percentage_blocking_score = json_decode($test_result, true)['percentage_blocking_score'];
      $percentage_blocking_score = $percentage_blocking_score * 100;
      $percentage_blocking_score = round($percentage_blocking_score, 2);

      // Generate random UUID of 32 characters
      $test_id = v4_UUID();

      // Insert the test result to  table wm_tests
      app('db')->insert('wm_tests', array(
          "test_id" => $test_id,
          "domain" => $domain,
          "test_result_json" => $test_result,
      ));

      // Select all domains from the database table wm_domains
      $users_registered_domains = app('db')->select(
          "SELECT `domain` FROM `wm_domains` LIMIT 1"
      );
      // Verify if $domain is already registered by a user
      $domain_registered_by_users = false;
      if ($users_registered_domains['domain'] == $domain) {
          $domain_registered_by_users = true;
          // Update to wm_domains
            app('db')->update('wm_domains', array(
              "latest_test_time" => date('Y-m-d H:i:s'),
              "latest_test_id" => $test_id,
              "latest_score" => $percentage_blocking_score,
          ), "domain = '$domain'");
      }
    
} else {
    $api_json = null;
}

?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="/docs/4.0/assets/img/favicons/favicon.ico">

    <title>WallMeter - GFW Blocking Detection</title>

    <!-- Bootstrap core CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <!-- highlight.js -->
    <link type="text/css" href="assets/index.css" rel="stylesheet"/>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.12.0/styles/ocean.min.css">
    <script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.12.0/highlight.min.js"></script>

  </head>

  <header class="p-3 bg-dark text-white">
      <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
          <a href="/" class="d-flex align-items-center mb-2 mb-lg-0 text-white text-decoration-none">
            <svg class="bi me-2" width="40" height="32" role="img" aria-label="Bootstrap"><use xlink:href="#bootstrap"></use></svg>
          </a>

          <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
            <li><a href="index.php" class="nav-link px-2 text-secondary">Home</a></li>
          </ul>

          <!-- <form class="col-12 col-lg-auto mb-3 mb-lg-0 me-lg-3">
            <input class="form-control form-control-dark" target="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" placeholder="Test a domain..." aria-label="Test">
          </form> -->

          <div class="text-end">
            <a type="button" class="btn btn-outline-light me-2" href="/user/login.php" target="_blank">Login</a>
            <a type="button" class="btn btn-primary" href="/user/login.php#create" target="_blank">Sign-up</a>
          </div>
        </div>
      </div>
    </header>

  <body class="bg-light">

    

    <div class="container">
      <div class="py-5 text-center">
        
        <h2>WallMeter</h2>
        <p class="lead">Detect if your website is blocked in China by testing from ISPs at multiple locations</p>
        <p class="lead">从中国大陆多个地区的不同运营商检测你的网站是否被屏蔽</p>
      </div>

      <div class="row">
        
        <div class="col-md-12 order-md-1">
          <h4 class="mb-3">Test Now</h4>
          <form id="queryForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">

            <?php if (!isset($domain)): ?>
            <div class="mb-3">
              <div class="col-md-12 mb-3">
                <label for="firstName">Domain name</label>
                <input type="text" class="form-control" id="domain" name="domain" placeholder="example.com" value="google.com" required>
                <input type="hidden" name="<?= ASCsrf::getTokenName() ?>" value="<?= ASCsrf::getToken() ?>">
                <div class="invalid-feedback">
                  Valid domain name is required.
                </div>
              </div>
            </div>
            <?php endif; ?>

            <?php if (isset($domain)): ?>

            <?php if ($shareable == 'yes'): ?>
            <div class="mb-3">
              <label>Shareable Result URL at PasteBin.com</label>
                <div class="alert alert-success" role="alert">
                    <a href="<?php echo $pastebin_url; ?>"><?php echo $pastebin_url; ?></a>
                </div>
            </div>
            <?php endif; ?>

            <div class="mb-3">
              <label>Shareable Result URL at WallMeter</label>
                <div class="alert alert-success" role="alert">
                    <a href="<?php echo WEBSITE_DOMAIN."/v1/test_data.php?id=".$test_id; ?>">
                      <?php echo WEBSITE_DOMAIN."/v1/test_data.php?id=".$test_id; ?>
                    </a>
                </div>
            </div>

            <div class="mb-3">
              <label>Result in JSON format</label>
                <pre><code>
                </code></pre>
                <script>
                    // Generated by CoffeeScript 2.0.1
                    (function() {
                    var data, init;
                    data = void 0;
                    init = async function() {
                        data = <?php echo $api_json; ?>;
                        // Set code text as the loaded prettified JSON
                        document.querySelector('code').innerHTML = JSON.stringify(data, null, "  ");
                        // Highlights the JSON
                        return hljs.highlightBlock(document.querySelector('code'));
                    };
                    init();
                    }).call(this);
                </script>

            </div>

            <?php endif; ?>

            <?php if (!isset($domain)): ?>

              <!-- Check box -->
              <div class="custom-control custom-checkbox mb-3">
                <input type="checkbox" class="custom-control-input" id="shareable_wm" name="shareable_wm" value="yes" checked disabled>
                <label class="custom-control-label" for="shareable">Make the results shareable at WallMeter</label>
              </div>
              
              <!-- Check box -->
              <div class="custom-control custom-checkbox mb-3">
                <input type="checkbox" class="custom-control-input" id="shareable" name="shareable" value="yes">
                <label class="custom-control-label" for="shareable">Make the results shareable at PasteBin.com</label>
              </div>

            <button class="btn btn-primary btn-lg btn-block" type="submit" id="submitButton" data-loading-text="Testing ...">Perform Testing</button>
            <?php endif; ?>

          </form>
        </div>
      </div>

    <!-- Divider -->
    <hr class="mb-4">

    <?php if (!isset($domain)): ?>
    <div class="row">
        <div class="col-md-12 order-md-1">
            <h5 class="mb-3">API v1 Endpoint</h4>
            /v1/api.php?domain=google.com
        </div>
    </div>

    <!-- Divider -->
    <hr class="mb-4">

    <div class="row">
        <div class="col-md-12 order-md-1">
            <h5 class="mb-3">API v1 Response Example</h4>
            <pre><code></code></pre>
            <script>
                // Generated by CoffeeScript 2.0.1
                (function() {
                var data, init;
                data = void 0;
                init = async function() {
                    // Data loading
                    data || (data = (await fetch('assets/example_response.json').then(function(response) {
                    return response.json();
                    })));
                    // Set code text as the loaded prettified JSON
                    document.querySelector('code').innerHTML = JSON.stringify(data, null, "  ");
                    // Highlights the JSON
                    return hljs.highlightBlock(document.querySelector('code'));
                };
                init();
                }).call(this);
            </script>
        </div>
    </div>
    <!-- Divider -->
    <hr class="mb-4">

    <?php endif; ?>

    <div class="row">
        <div class="col-md-12 order-md-1">
            <h5 class="mb-3">API v1 Response Fields</h4>
            Per node:
              <ul>
              <li><code>blocked</code> - whether the service is blocked from a node</li>
              <li><code>as_detected_cn</code> - AS number detected by the node in China</li>
              <li><code>as_detected_us</code> - AS number detected by the node in the US</li>
              <li><code>ip_detected_cn</code> - IP detected by the node in China</li>
              <li><code>ip_detected_us</code> - IP detected by the node in the US</li>
              <li><code>web_server_status_cn</code> - Port check on 80 and 443 performed by the node in China</li>
              <li><code>web_server_status_us</code> - Port check on 80 and 443 performed by the node in the US</li>
              </ul>
            Overall:
              <ul>
              <li><code>percentage_blocking_score</code> - A percentage score ranged from 0 to 1 representing how likely is the service blocked (1 = most likely)</li>
              <li><code>evaluation</code> - a text description of the testing result</li>
              </ul>
        </div>
    </div>

    <!-- Divider -->
    <hr class="mb-4">

    <div class="row">
        <div class="col-md-12 order-md-1">
            <h5 class="mb-3">Limitations</h4>
            False positive scenarios:
              <ul>
                <li>when a domain uses CDN, load balancer, or GeoDNS that resolves to IPs belong to different ASNs</li>
              </ul>
            Other considerations:
              <ul>
                <li>unstable connection to the remote API servers</li>
                <li>slow response time that leads to timeout</li>
                <li>DNS query denied by ISP's DNS servers</li>
              </ul>
        </div>
    </div>

    <!-- Divider -->
    <hr class="mb-4">

      <footer class="my-5 pt-5 text-muted text-center text-small">
        <p class="mb-1">&copy; O3O.CA</p>
        <ul class="list-inline">
          <li class="list-inline-item"><a href="https://home.o3o.ca/" target="_blank">Home</a></li>
        </ul>
      </footer>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="loadingModal" tabindex="-1" role="dialog" aria-labelledby="loadingModal" aria-hidden="true" style="z-index: 9999; background-color: rgba(0, 0, 0, 0.4);">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="loadingModalLabel">Testing...</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <p><i class="fa fa-spinner fa-spin fa-3x fa-fw"></i> Testing... Please wait for a few minutes.</p>
            <!-- webp image -->
            <img src="assets/img/loading.webp" width=200 alt="Loading...">
          </div>
        </div>
      </div>
    </div>

    <!-- Bootstrap core JavaScript -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <!-- bootstrap.min.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>

    <!-- Show modal when form queryForm is submitted -->
    <script>
      $('#queryForm').on('submit', function () {
        $('#loadingModal').modal('show');
      });
    </script>
    
    
  </body>
</html>
