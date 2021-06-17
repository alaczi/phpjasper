#This is a simple script to start-stop Java Bridge
#and automatically include jars from the JAR_DIR
SCRIPT_PATH="$( cd -- "$(dirname "$0")" >/dev/null 2>&1 ; pwd -P )"
JAR_DIR=$SCRIPT_PATH"/libs/"
LOG_FILE="./logfile"
PID_FILE="./pid"
USER_GUID=""

THE_CLASSPATH=""
EFFECTIVE_GUID=$([ "$USER_GUID" ] && echo "$USER_GUID" || echo "$USER")
JAVA=$(which java)

for i in `ls "${JAR_DIR}"*.jar`
do
    THE_CLASSPATH=${THE_CLASSPATH}:${i}
done


PRSRV_ARGS=" php.java.bridge.JavaBridge SERVLET_LOCAL:8081 1 "${LOG_FILE}
PRSRV_ARGS="-Djava.awt.headless=true -classpath "$THE_CLASSPATH" "$PRSRV_ARGS

case "$1" in
    start)
        echo -n "Starting JavaBridge..."
        export LANG=hu_HU
        start-stop-daemon --start --quiet --chuid ${EFFECTIVE_GUID} --oknodo --background --make-pidfile --pidfile ${PID_FILE} --exec ${JAVA} -- ${PRSRV_ARGS}
        echo "JavaBridge started."
     ;;
     stop)
        echo "Stopping JavaBridge... "
        start-stop-daemon --stop --quiet --chuid ${EFFECTIVE_GUID} --oknodo --name --make-pidfile --pidfile ${PID_FILE}
        kill -TERM `cat $PID_FILE`
        rm $PID_FILE
        echo "JavaBridge stopped."
      ;;
      *)
        echo "Usage: $0 {start|stop}" >&2
        exit 1
esac