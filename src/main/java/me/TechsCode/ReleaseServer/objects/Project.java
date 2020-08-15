package me.TechsCode.ReleaseServer.objects;

import java.util.List;
import java.util.Optional;

public class Project {

    private String name;
    private String githubRepository;
    private String githubToken;
    private List<Deployment> deployments;

    public Project(String name, String githubRepository, String githubToken, List<Deployment> deployments) {
        this.name = name;
        this.githubRepository = githubRepository;
        this.githubToken = githubToken;
        this.deployments = deployments;
    }

    public String getName() {
        return name;
    }

    public String getGithubRepository() {
        return githubRepository;
    }

    public Optional<String> getGithubToken() {
        return Optional.ofNullable(githubToken);
    }

    public List<Deployment> getDeployments() {
        return deployments;
    }
}
