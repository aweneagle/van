<?php
    include "van.php";
        
        class Job implements IJob {

            public function run($params){

                /*  now  "io" is a mysql interface */

                van_link("io", new IoMysql("host", "port", "user", "passwd"));

                van_set_state("io", "connected");
                $something = van_read("io", "select something from db");
                update($something);
                van_write("io", "update $something into db");
                van_set_state("io", "closed");
    
                //van_unlink("io"); // here we will auto unlink whenever no link points to IoMySql obj



                /*  now "io" is a map interface */

                van_link("io", new MapCsv2Arr);
                $csv_arr = van_map("io", file_get_contents("example.csv"));

                

                van_link("map", "io");
                /*  now "io" is a log file , and the "map" becomes to the map*/

                van_link("io", IoLogfile("/tmp/log"));
                van_write("io", json_encode(van_map("map", file_get_contents("example.csv"))));
            }
        }

        van_link("job_01", new Job);
        van_run("job_01","some params");
        van_unlink("job_01");


