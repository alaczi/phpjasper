#This is a simple script to start-stop Java Bridge
#and automatically include jars from the JAR_DIR
JAR_DIR="/your/jars/"
LOG_FILE="/var/log/java-bridge/logfile"
PID_FILE="/var/log/java-bridge/pid"
THE_CLASSPATH=""

for i in `ls "${JAR_DIR}"*.jar`
do
    THE_CLASSPATH=${THE_CLASSPATH}:${i}
done


PRSRV_ARGS=" php.java.bridge.JavaBridge SERVLET_LOCAL:8081 1 "${LOG_FILE}
PRSRV_ARGS=" -Djava.awt.headless=true -classpath "$THE_CLASSPATH" "$PRSRV_ARGS

case "$1" in
    start)
        echo -n "Starting JavaBridge..."
        export LANG=hu_HU
        start-stop-daemon --start --quiet --chuid www-data --oknodo --background --make-pidfile --pidfile ${PID_FILE} --exec /usr/bin/java -- ${PRSRV_ARGS}
        echo "JavaBridge started."
     ;;
     stop)
        echo "Stopping JavaBridge... "
        start-stop-daemon --stop --quiet --chuid www-data --oknodo --name --make-pidfile --pidfile ${PID_FILE}
        kill -TERM `cat $PID_FILE`
        rm $PID_FILE
        echo "JavaBridge stopped."
      ;;
      *)
        echo "Usage: $0 {start|stop|restart}" >&2
        exit 1
esac