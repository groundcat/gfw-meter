# GFW detector

Detect if your website is blocked in China by testing with servers at multiple locations.

## Requirements

- PHP 5.4+
- The PHP INI setting mbstring.func_overload equals 0, 1, 4, or 5.
- Install [Net_DNS2](https://netdns2.com/) using `composer require pear/net_dns2`

## Configuration

1. Replace `REPLACE_ME` in the `config.php` file with your configurations.
2. Deploy `detect.php`, `config.php` and `functions.php` at a server located in China (or other Internet-censored areas).
3. Deploy `v1/api.php`, `config.php`, `functions.php` and remaning files at a server located in a non-censored country.

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
  "domain": "google.com",
  "beijing_chinatelecom": {
    "blocked": 1,
    "result": "detected possible TCP connection reset",
    "as_detected_cn": "AS15169 Google LLC",
    "as_detected_us": "AS15169 Google LLC",
    "ip_detected_cn": "142.251.43.14",
    "ip_detected_us": "172.217.215.100",
    "web_server_status_cn": "down",
    "web_server_status_us": "up",
    "web_server_status_match": 0
  },
  "shanghai_unicom": {
    "blocked": 1,
    "result": "detected possible TCP connection reset",
    "as_detected_cn": "AS15169 Google LLC",
    "as_detected_us": "AS15169 Google LLC",
    "ip_detected_cn": "142.251.42.238",
    "ip_detected_us": "172.217.215.100",
    "web_server_status_cn": "down",
    "web_server_status_us": "up",
    "web_server_status_match": 0
  },
  "beijing_unicom": {
    "blocked": 1,
    "result": "detected possible DNS pollution",
    "as_detected_cn": "AS3356 Level 3 Parent, LLC",
    "as_detected_us": "AS15169 Google LLC",
    "ip_detected_cn": "8.7.198.46",
    "ip_detected_us": "172.217.215.100",
    "web_server_status_cn": "down",
    "web_server_status_us": "up",
    "web_server_status_match": 0
  },
  "beijing_tencentcloud": {
    "blocked": 1,
    "result": "detected possible DNS pollution",
    "as_detected_cn": "AS4766 Korea Telecom",
    "as_detected_us": "AS15169 Google LLC",
    "ip_detected_cn": "59.24.3.174",
    "ip_detected_us": "172.217.215.100",
    "web_server_status_cn": "down",
    "web_server_status_us": "up",
    "web_server_status_match": 0
  },
  "wulanchabu_aliyun": {
    "blocked": 1,
    "result": "detected possible TCP connection reset",
    "as_detected_cn": "AS15169 Google LLC",
    "as_detected_us": "AS15169 Google LLC",
    "ip_detected_cn": "172.217.163.46",
    "ip_detected_us": "172.217.215.100",
    "web_server_status_cn": "down",
    "web_server_status_us": "up",
    "web_server_status_match": 0
  },
  "percentage_blocking_score": 1,
  "evaluation": "high possibility of blocking detected",
  "code": 200
}
```
