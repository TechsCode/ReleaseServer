package me.TechsCode.ReleaseServer;

import com.jcraft.jsch.*;
import me.TechsCode.ReleaseServer.objects.Deployment;
import me.TechsCode.ReleaseServer.objects.Project;
import me.TechsCode.ReleaseServer.objects.Remote;
import org.apache.commons.io.FileUtils;
import org.apache.commons.io.IOUtils;

import java.io.*;
import java.nio.charset.StandardCharsets;
import java.util.*;
import java.util.stream.Collectors;

public abstract class DeploymentManager extends Thread {

    private static final int DELAY = 1000 * 10;

    public static JSch jsch = new JSch();

    private List<Integer> previousReleases;

    public DeploymentManager() {
        this.previousReleases = new ArrayList<>();

        start();
    }

    public abstract List<Artifact> getArtifacts();

    @Override
    public void run() {
        while (true){
            List<Artifact> artifacts = getArtifacts();

            if(!previousReleases.isEmpty()){
                artifacts.stream().filter(artifact -> !previousReleases.contains(artifact.getRelease().getId()))
                        .forEach(artifact -> {
                            Project project = artifact.getRelease().getProject();

                            if(artifact.getAssets().length == 0){
                                System.out.println("["+project.getName()+"] The Release ["+artifact.getReleaseTag()+"] does not contain any assets, skipping deployment");
                                return;
                            }

                            for (Deployment deployment : project.getDeployments()) {
                                if (!deployment.isEnabled()) continue;

                                Remote remote = deployment.getRemote();

                                try {
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
                                    System.out.println("[" + project.getName() + "] Deployment of release [" + artifact.getReleaseTag() + "] to remote [" + remote.getHostname() + "] was successful");
                                } catch (JSchException | SftpException | IOException e) {
                                    System.out.println("[" + project.getName() + "] Error while deploying release [" + artifact.getReleaseTag() + "] to [" + remote.getHostname() + "]");
                                    System.out.println(e.getMessage());
                                }
                            }
                        });
            }

            this.previousReleases = artifacts.stream().map(a -> a.getRelease().getId()).collect(Collectors.toList());

            try {
                sleep(DELAY);
            } catch (InterruptedException e) {
                e.printStackTrace();
            }
        }
    }
}