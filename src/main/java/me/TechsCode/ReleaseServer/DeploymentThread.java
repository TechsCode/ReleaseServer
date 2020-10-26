package me.TechsCode.ReleaseServer;

import com.jcraft.jsch.ChannelExec;
import com.jcraft.jsch.ChannelSftp;
import com.jcraft.jsch.JSch;
import com.jcraft.jsch.Session;
import me.TechsCode.ReleaseServer.objects.Deployment;
import me.TechsCode.ReleaseServer.objects.Project;
import me.TechsCode.ReleaseServer.objects.Remote;
import org.apache.commons.io.IOUtils;

import java.io.File;
import java.io.FileInputStream;
import java.io.StringWriter;
import java.nio.charset.StandardCharsets;
import java.util.Properties;

public class DeploymentThread implements Runnable {

    public static JSch jsch = new JSch();

    private final Artifact artifact;

    public DeploymentThread(Artifact artifact) {
        this.artifact = artifact;

        new Thread(this).start();
    }

    @Override
    public void run() {
        Project project = artifact.getRelease().getProject();

        if(artifact.getAssets().length == 0){
            System.out.println("["+project.getName()+"] The Release ["+artifact.getReleaseTag()+"] does not contain any assets, skipping deployment");
            return;
        }

        if(project.getDeployments().isEmpty()){
            System.out.println("["+project.getName()+"] No deployments configured for this project");
            return;
        }

        System.out.println("["+project.getName()+"] Deploying to remotes... awaiting completion..");

        for (Deployment deployment : project.getDeployments()) {
            if (deployment.isEnabled()){
                try {
                    deploy(deployment);
                    System.out.println("[" + project.getName() + "] Successfully completed deployment of release [" + artifact.getReleaseTag() + "] to remote [" + deployment.getRemote().getHostname() + "]!");
                } catch (Exception e){
                    System.out.println("[" + project.getName() + "] Error while deploying release [" + artifact.getReleaseTag() + "] to [" + deployment.getRemote().getHostname() + "]");
                    System.out.println(e.getMessage());
                }
            }
        }
    }

    private void deploy(Deployment deployment) throws Exception {
        Remote remote = deployment.getRemote();

        Properties config = new Properties();
        config.put("StrictHostKeyChecking", "no");

        Session session = jsch.getSession(remote.getUsername(), remote.getHostname(), remote.getPort());
        session.setPassword(remote.getPassword());
        session.setConfig(config);
        session.connect();

        ChannelSftp sftp = (ChannelSftp) session.openChannel("sftp");
        sftp.connect();
        sftp.cd(deployment.getPath());

        for (File asset : artifact.getAssets()) {
            sftp.put(new FileInputStream(asset), asset.getName(), ChannelSftp.OVERWRITE);
        }

        sftp.exit();

        for (String command : deployment.getCommands()) {
            ChannelExec channel = (ChannelExec) session.openChannel("exec");
            channel.setCommand(command);
            channel.connect();

            StringWriter writer = new StringWriter();
            IOUtils.copy(channel.getInputStream(), writer, StandardCharsets.UTF_8);
            String output = writer.toString();

            System.out.println(output);

            channel.disconnect();
        }

        session.disconnect();
    }
}
