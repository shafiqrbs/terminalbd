#Current Timestamps
now=$(date +'%d-%m-%Y_%H:%M:%S')

#Backup server details
backup_server_ip=45.77.46.170
backup_server_user=root
backup_server_password="?q2Bz3Q@y1p(%p6p"

#Mysql details
host="localhost"
mysql_user="root"
mysql_password="*rbs*terminalbd#"
mysql_db_name="terminalbd"

#Current server IP
current_server_ip=$(hostname --all-ip-addresses | awk '{print $1}')

#Directory in current server
backup_dir="/home/DB_backup/$(date +%Y)/$(date +%b)"

#backup_dir="/home/DB_backup/$(date +%Y)/$(date +%b)"
db_name_new="terminalbd_${now}.sql.gz"
#db_name="sample.txt"

#Check if directory is available
if [ ! -d "$backup_dir" ]; then
  mkdir $backup_dir
 # sudo chmod -R 0777 $backup_dir
fi
echo "Creating backup..."
mysqldump --user=${mysql_user} --password=${mysql_password} --host=${host} ${mysql_db_name} >${backup_dir}${db_name_new}
echo "Backup Done!"

     $filename = "terminaldb-" . date("d-m-Y") . ".sql.gz";
        $mime = "application/x-gzip";

        header( "Content-Type: " . $mime );
        header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
        $cmd = "mysqldump -u $DBUSER --password=$DBPASSWD $DATABASE | gzip --best";
        passthru( $cmd );

echo "Checking directory..."
sshpass -p ${backup_server_password} ssh ${backup_server_user}@${backup_server_ip} mkdir -p /home/DB_backup/${current_server_ip}/$(date +%Y)/$(date +%b)
echo "Directory found!"

echo "Transferring to backup server..."
sshpass -p ${backup_server_password} scp ${backup_dir}${db_name_new} ${backup_server_user}@${backup_server_ip}:/home/DB_backup/${current_server_ip}/$(date +%Y)/$(date +%b)/
echo "Transfer Completed!"
