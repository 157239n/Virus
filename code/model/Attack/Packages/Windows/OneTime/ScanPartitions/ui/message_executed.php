<p>The partitions/drives available are:</p>
<ul>
    <?php use function Kelvinho\Virus\map;

    map($attack->getAvailableDrives(), function ($drive) { ?>
        <li>
            <pre><?php echo $drive ?>:/</pre>
        </li>
    <?php }); ?>
</ul>