ps aux | grep check.php | awk -F" " '{print $2}' | xargs kill -9
nohup php check.php > /dev/null 2>&1 &

#ps aux | grep success.php | awk -F" " '{print $2}' | xargs kill -9
#nohup php up.php >> ./.up 2>&1 &
