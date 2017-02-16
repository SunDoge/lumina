# A Tiny PHP Framework

Learn Lumen and Laravel.

## Goal

Understand how Lumen and Laravel work by imitating the code and finally build my own framework.

## TODO

- [x] Make `router` work.
- [x] Make `controller` work.
- [ ] Modify the `Request`.
- [ ] Modify the `Response`.
- [ ] Write redis driver and add in `hredis`.

***
Run

```php
php public/index.php
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