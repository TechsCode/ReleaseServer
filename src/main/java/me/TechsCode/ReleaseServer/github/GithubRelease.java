package me.TechsCode.ReleaseServer.github;

import org.kohsuke.github.GHAsset;
import org.kohsuke.github.GHRelease;

import java.io.File;
import java.io.IOException;
import java.util.List;

public class GithubRelease {

    private final GHRelease release;
    private GHAsset asset = null;

    public GithubRelease(GHRelease release) throws IOException {
        List<GHAsset> releaseAsset = release.listAssets().toList();
        if(releaseAsset.size() > 0) {
            this.asset = releaseAsset.get(0);
        }
        this.release = release;
    }

    public GHRelease getRelease() {
        return release;
    }

    public GHAsset getAsset() {
        return asset;
    }
}