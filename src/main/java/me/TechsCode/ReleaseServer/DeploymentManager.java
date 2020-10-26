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
                artifacts.stream().filter(artifact -> !previousReleases.contains(artifact.getRelease().getId())).forEach(DeploymentThread::new);
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