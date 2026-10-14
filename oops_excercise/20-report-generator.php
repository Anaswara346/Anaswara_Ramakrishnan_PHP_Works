<!-- Dependency Injection: a class receives the object it needs from outside instead of creating it itself. -->
<!-- Example: ReportGenerator depends on a datasource object. -->
<?php

interface DatasourceInterface{
    public function getData();  //returns the data that will be used in the report.
}

class MySQLDataSource implements DatasourceInterface{
    public function getData(){
        return ["user1", "user2", "user3"];
    }
}

class FileDataSource implements DatasourceInterface{
    public function getData(){
        return ["File1", "File 2"];
    }
}

class ReportGenerator{
    private $dataSource;

    public function __construct(DatasourceInterface $dataSource){
        $this->dataSource = $dataSource;
    }

    public function generateReport(){
        $data = $this->dataSource->getData();
        foreach($data as $item){
            echo $item . "\n";
        }
    }
}

echo "From the MySQL Data Source\n";
$dataSource = new MySQLDataSource();
$report = new ReportGenerator($dataSource);
$report->generateReport();
echo "From the File Data Source\n";
$fileDataSource = new FileDataSource();
$report1 = new ReportGenerator($fileDataSource);
$report1->generateReport();


?>
