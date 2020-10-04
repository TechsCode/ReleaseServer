package me.TechsCode.ReleaseServer;

import me.TechsCode.ReleaseServer.objects.Project;
import org.springframework.boot.SpringApplication;
import org.springframework.boot.autoconfigure.SpringBootApplication;

import java.util.ArrayList;
import java.util.Collections;
import java.util.List;
import java.util.Optional;

@SpringBootApplication
public class ReleaseServer {

    private static List<Project> projects;
    private static List<Release> releases;
    private static List<Artifact> artifacts;

    public static void main(String[] args){
        Config config = Config.getInstance();

        SpringApplication app = new SpringApplication(ReleaseServer.class);
        app.setDefaultProperties(Collections.singletonMap("server.port", config.getPort()));
        app.run(args);

        ReleaseServer.projects = config.getProjects();

        new GithubReleaseFetcher(projects){
            @Override
            public void onRetrieve(List<Release> releases) {
                ReleaseServer.releases = releases;
            }
        };

        new ArtifactRepository(){
            @Override
            public List<Release> getReleases() {
                return releases;
            }

            @Override
            public void onRetrieve(List<Artifact> artifacts) {
                ReleaseServer.artifacts = artifacts;
            }
        };

        new DeploymentManager(){
            @Override
            public List<Artifact> getArtifacts() {
                return artifacts;
            }
        };
    }

    public static List<Artifact> getArtifacts() {
        return artifacts;
    }
}


