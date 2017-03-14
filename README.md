<div class="danger">
This framework is far from being completed.
</dive>

# Lumina

## A Tiny PHP Framework

As elegant as `Laravel`, as fast as `express`.

## Goal

Understand how Lumen and Laravel work by imitating the code and finally build my own framework.

## TODO

- [x] Make `router` work.
- [x] Make `controller` work.
- [x] Modify the `Request`.
- [x] Modify the `Response`.
- [ ] Abandon `cli mode`.
- [ ] Handle exceptions under cli mode.
- [ ] Write `Mysql` driver.
- [ ] Write `Redis` driver and add in `hredis`.

***
Run

```php
php bootstrap/http.php
```

***

Benchmark(2017/02/16) `return 'Hello world!;`

```text
➜  Sites ab -t 10 -c 100 http://127.0.0.1:8888/
This is ApacheBench, Version 2.3 <$Revision: 1748469 $>
Copyright 1996 Adam Twiss, Zeus Technology Ltd, http://www.zeustech.net/
Licensed to The Apache Software Foundation, http://www.apache.org/

Benchmarking 127.0.0.1 (be patient)
Completed 5000 requests
Completed 10000 requests
Completed 15000 requests
Finished 15243 requests


Server Software:        swoole-http-server
Server Hostname:        127.0.0.1
Server Port:            8888

Document Path:          /
Document Length:        59 bytes

Concurrency Level:      100
Time taken for tests:   10.043 seconds
Complete requests:      15243
Failed requests:        0
Total transferred:      3162753 bytes
HTML transferred:       901461 bytes
Requests per second:    1517.72 [#/sec] (mean)
Time per request:       65.888 [ms] (mean)
Time per request:       0.659 [ms] (mean, across all concurrent requests)
Transfer rate:          307.53 [Kbytes/sec] received

Connection Times (ms)
              min  mean[+/-sd] median   max
Connect:        1   28  20.4     21     107
Processing:     2   37  21.9     30     120
Waiting:        1   23  17.1     19     102
Total:         12   65  31.8     59     162

Percentage of the requests served within a certain time (ms)
  50%     59
  66%     79
  75%     88
  80%     94
  90%    110
  95%    125
  98%    135
  99%    140
 100%    162 (longest request)
```

## Compare to express

2017/03/02 Macbook Pro 13 2015

echo `json` {"status":0} 

### cgi

```text
➜  ~ ab -n 15000 -c 20 http://a-tiny-php-framework.dev/controller/
This is ApacheBench, Version 2.3 <$Revision: 1748469 $>
Copyright 1996 Adam Twiss, Zeus Technology Ltd, http://www.zeustech.net/
Licensed to The Apache Software Foundation, http://www.apache.org/

Benchmarking a-tiny-php-framework.dev (be patient)
Completed 1500 requests
Completed 3000 requests
Completed 4500 requests
Completed 6000 requests
Completed 7500 requests
Completed 9000 requests
Completed 10500 requests
Completed 12000 requests
Completed 13500 requests
Completed 15000 requests
Finished 15000 requests


Server Software:        nginx/1.10.1
Server Hostname:        a-tiny-php-framework.dev
Server Port:            80

Document Path:          /controller/
Document Length:        12 bytes

Concurrency Level:      20
Time taken for tests:   20.812 seconds
Complete requests:      15000
Failed requests:        0
Total transferred:      2805000 bytes
HTML transferred:       180000 bytes
Requests per second:    720.75 [#/sec] (mean)
Time per request:       27.749 [ms] (mean)
Time per request:       1.387 [ms] (mean, across all concurrent requests)
Transfer rate:          131.62 [Kbytes/sec] received

Connection Times (ms)
              min  mean[+/-sd] median   max
Connect:        0    3   3.9      2      32
Processing:     3   23   7.6     22      98
Waiting:        3   23   7.6     22      98
Total:          7   27   8.2     27      98

Percentage of the requests served within a certain time (ms)
  50%     27
  66%     31
  75%     33
  80%     34
  90%     37
  95%     39
  98%     44
  99%     48
 100%     98 (longest request)
```


### cli

```text
➜  ~ ab -n 15000 -c 20 http://127.0.0.1:8888/controller/
This is ApacheBench, Version 2.3 <$Revision: 1748469 $>
Copyright 1996 Adam Twiss, Zeus Technology Ltd, http://www.zeustech.net/
Licensed to The Apache Software Foundation, http://www.apache.org/

Benchmarking 127.0.0.1 (be patient)
Completed 1500 requests
Completed 3000 requests
Completed 4500 requests
Completed 6000 requests
Completed 7500 requests
Completed 9000 requests
Completed 10500 requests
Completed 12000 requests
Completed 13500 requests
Completed 15000 requests
Finished 15000 requests


Server Software:        swoole-http-server
Server Hostname:        127.0.0.1
Server Port:            8888

Document Path:          /controller/
Document Length:        12 bytes

Concurrency Level:      20
Time taken for tests:   11.128 seconds
Complete requests:      15000
Failed requests:        0
Total transferred:      2730000 bytes
HTML transferred:       180000 bytes
Requests per second:    1347.91 [#/sec] (mean)
Time per request:       14.838 [ms] (mean)
Time per request:       0.742 [ms] (mean, across all concurrent requests)
Transfer rate:          239.57 [Kbytes/sec] received

Connection Times (ms)
              min  mean[+/-sd] median   max
Connect:        0    6   4.0      4      58
Processing:     1    9   4.9      8      60
Waiting:        0    6   3.9      5      59
Total:          3   14   6.7     13      66

Percentage of the requests served within a certain time (ms)
  50%     13
  66%     17
  75%     19
  80%     20
  90%     23
  95%     25
  98%     28
  99%     31
 100%     66 (longest request)
```

### express

```text
➜  ~ ab -n 15000 -c 20 http://127.0.0.1:8888/controller/
This is ApacheBench, Version 2.3 <$Revision: 1748469 $>
Copyright 1996 Adam Twiss, Zeus Technology Ltd, http://www.zeustech.net/
Licensed to The Apache Software Foundation, http://www.apache.org/

Benchmarking 127.0.0.1 (be patient)
Completed 1500 requests
Completed 3000 requests
Completed 4500 requests
Completed 6000 requests
Completed 7500 requests
Completed 9000 requests
Completed 10500 requests
Completed 12000 requests
Completed 13500 requests
Completed 15000 requests
Finished 15000 requests


Server Software:
Server Hostname:        127.0.0.1
Server Port:            8888

Document Path:          /controller/
Document Length:        12 bytes

Concurrency Level:      20
Time taken for tests:   12.103 seconds
Complete requests:      15000
Failed requests:        0
Total transferred:      3195000 bytes
HTML transferred:       180000 bytes
Requests per second:    1239.36 [#/sec] (mean)
Time per request:       16.137 [ms] (mean)
Time per request:       0.807 [ms] (mean, across all concurrent requests)
Transfer rate:          257.80 [Kbytes/sec] received

Connection Times (ms)
              min  mean[+/-sd] median   max
Connect:        0    1   0.9      1      15
Processing:     1   15   5.3     16      38
Waiting:        0   15   5.3     16      38
Total:          3   16   5.5     17      39

Percentage of the requests served within a certain time (ms)
  50%     17
  66%     20
  75%     20
  80%     21
  90%     22
  95%     23
  98%     25
  99%     27
 100%     39 (longest request)
```
