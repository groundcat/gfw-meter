# GFW detector

## Features

- Detect if your website is blocked in a censored country by testing with ISPs at multiple locations and compare with the results of another country.
- Keep a record of testing results in a MySQL database.
- Web UI for admin registration and login to access a dashboard, add monitors, and get weekly summary reports.

## Approach

### DNS poisoning detection

- Get ASN of IPs resolved by DNS at different ISPs in the censored country.
- Get ASN of IPs resolved by the Cloudflare DNS in a non-censored country.
- Compare the ASN to determine if they match.

### TCP reset attack detection

- Get `TCPing` responses at ports `80` and `443` of IPs resolved by DNS at different ISPs in the censored country.
- Get `TCPing` responses at ports `80` and `443` of IPs resolved by the Cloudflare DNS in a non-censored country.
- Compare the TCPing responses to determine if they match.

### Self protection mechanism

The detection program implemented at a server in the censored country first calls external APIs for IP validation and TCP reset attack detection. This prevents the target tested server from logging the IP address of your testing node. 

However, if the external TCPing API is not reachable, the program will fallback to the localhost method of testing with TCPing.

Although using `cURL` could be a more reliable approach than `TCPing` and there is a function `website_content_md5()` in the `functions.php` that hashes data into a `md5` string for comparison, it is not used for now, because using this method has higher risks of your node's IP address getting logged by the target web server.

### Notes on DNS surveillance

There is no mechanism implemented here to protect your testing node from any DNS surveillance. The DNS query is performed locally with [Net_DNS2](https://netdns2.com/).

## Requirements

- PHP 5.4+
- The PHP INI setting `mbstring.func_overload` equals 0, 1, 4, or 5.
- Install [Net_DNS2](https://netdns2.com/) using `composer require pear/net_dns2`

## Configuration and Implementation

1. Replace `REPLACE_ME` in the `config.php` file with your configurations.
2. Deploy and configure `detect.php`, `config.php`, and `functions.php` at a server located in a censored country.
3. Deploy and configure `config.php`, `user/ASEngine/ASConfig.php`, and remaning files at a server located in a non-censored country.

## API

Request example

```
/v1/api.php?domain=google.com
```

Response fields

- `blocked` - whether the service is blocked from a node
- `as_detected_cn` - AS number detected by the node in China
- `as_detected_us` - AS number detected by the node in the US
- `ip_detected_cn` - IP detected by the node in China
- `ip_detected_us` - IP detected by the node in the US
- `web_server_status_cn` - Port check performed by the node in China
- `web_server_status_us` - Port check performed by the node in the US
- `percentage_blocking_score` - A score ranged from 0 to 1 representing the possibility of the service being blocked
- `evaluation` - a text description of the testing result

Response example

```
{
  "domain": "facebook.com",
  "beijing_chinatelecom": {
    "blocked": 1,
    "result": "possible TCP reset attack",
    "as_detected_cn": "AS32934 Facebook, Inc.",
    "as_detected_us": "AS32934 Facebook, Inc.",
    "ip_detected_cn": "69.171.242.11",
    "ip_detected_us": "31.13.70.36",
    "web_server_status_cn": "down",
    "web_server_status_us": "up",
    "web_server_status_match": 0
  },
  "shanghai_unicom": {
    "blocked": 1,
    "result": "possible DNS poisoning and TCP reset attack",
    "as_detected_cn": "AS19679 Dropbox, Inc.",
    "as_detected_us": "AS32934 Facebook, Inc.",
    "ip_detected_cn": "108.160.170.39",
    "ip_detected_us": "31.13.70.36",
    "web_server_status_cn": "down",
    "web_server_status_us": "up",
    "web_server_status_match": 0
  },
  "beijing_unicom": {
    "blocked": 1,
    "result": "possible DNS poisoning and TCP reset attack",
    "as_detected_cn": "AS13414 Twitter Inc.",
    "as_detected_us": "AS32934 Facebook, Inc.",
    "ip_detected_cn": "199.59.150.13",
    "ip_detected_us": "31.13.70.36",
    "web_server_status_cn": "down",
    "web_server_status_us": "up",
    "web_server_status_match": 0
  },
  "beijing_tencentcloud": {
    "blocked": 1,
    "result": "possible DNS poisoning and TCP reset attack",
    "as_detected_cn": "AS19679 Dropbox, Inc.",
    "as_detected_us": "AS32934 Facebook, Inc.",
    "ip_detected_cn": "108.160.162.31",
    "ip_detected_us": "31.13.70.36",
    "web_server_status_cn": "down",
    "web_server_status_us": "up",
    "web_server_status_match": 0
  },
  "wulanchabu_aliyun": {
    "blocked": 1,
    "result": "possible TCP reset attack",
    "as_detected_cn": "AS32934 Facebook, Inc.",
    "as_detected_us": "AS32934 Facebook, Inc.",
    "ip_detected_cn": "69.63.176.59",
    "ip_detected_us": "31.13.70.36",
    "web_server_status_cn": "down",
    "web_server_status_us": "up",
    "web_server_status_match": 0
  },
  "percentage_blocking_score": 1,
  "evaluation": "high possibility of blocking",
  "code": 200,
  "timestamp": 1649009564
}
```

## Web UI and MySQL database

Deploy the program with instructions above and import the `db_demo.sql` MySQL database.

Demo users:

- `admin:admin123`
- `test:test123`

## Set up scheduled tasks

Deploy a job to schedule run `run_test.php` at an acceptable frequency.

```
# run every hour
0 * * * * php run_test.php
```

# Limitations

## False positive scenarios

- when a domain uses CDN, load balancer, or GeoDNS that resolves to IPs belong to different ASNs

## Other considerations

- unstable connection to the remote API servers
- slow response time that leads to timeout
- DNS query denied by ISP's DNS servers