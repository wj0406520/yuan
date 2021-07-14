ps aux | grep check.php | awk -F" " '{print $2}' | xargs kill -9

ps aux | grep t.php | awk -F" " '{print $2}' | xargs kill -9
