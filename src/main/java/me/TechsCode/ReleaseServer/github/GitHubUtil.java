package me.TechsCode.ReleaseServer.github;

import org.kohsuke.github.*;

import java.io.IOException;
import java.util.Objects;

public class GitHubUtil {

    private static GitHub github = null;

    public static GithubRelease getLatestRelease(String token, String repo) {
        try {
            GHRepository ghrepo = Objects.requireNonNull(getGithub(token)).getRepository(repo);
            GHRelease release = ghrepo.getLatestRelease();

            if(release == null) return null;

            return new GithubRelease(release);
        } catch (IOException e) {
            e.printStackTrace();
            return null;
        }
    }

    private static GitHub getGithub(String token) {
        try {
            if(github == null) github = new GitHubBuilder().withOAuthToken(token).build();
            return github;
        } catch (IOException e) {
            e.printStackTrace();
            return null;
        }
    }
}
