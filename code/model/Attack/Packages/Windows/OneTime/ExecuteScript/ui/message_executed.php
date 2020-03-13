<p>This attack is executed. Here are contents of file %~pd0data:</p>
<pre style="overflow: auto;"><?php echo htmlspecialchars($attack->getData()); ?></pre>
<p>And the contents of file %~pd0err:</p>
<pre style="overflow: auto;"><?php echo htmlspecialchars($attack->getError()); ?></pre>