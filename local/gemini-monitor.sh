 #! /bin/bash
### BEGIN INIT INFO
# Provides: testdaemon
# Required-Start:
# Should-Start:
# Required-Stop:
# Should-Stop:
# Default-Start:  3 5
# Default-Stop:   0 1 2 6
# Short-Description: Gemini Monitor
# Description:    Gemini NAS LED status (hard disk sleep) reporter
### END INIT INFO

case "$1" in
  start)
    echo "Starting server"
    # Start the daemon
    python3 /root/gemini-monitor.py start
    ;;
  stop)
    echo "Stopping server"
    # Stop the daemon
    python3 /root/gemini-monitor.py stop
    ;;
  restart)
    echo "Restarting server"
    python3 /root/gemini-monitor.py restart
    ;;
  *)
    # Refuse to do other stuff
    echo "Usage: /etc/init.d/gemini-monitor.sh {start|stop|restart}"
    exit 1
    ;;
esac

exit 0