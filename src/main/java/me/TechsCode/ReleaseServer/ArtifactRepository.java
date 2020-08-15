package me.TechsCode.ReleaseServer;

import org.apache.commons.io.FileUtils;

import javax.net.ssl.HttpsURLConnection;
import java.io.File;
import java.io.FileOutputStream;
import java.io.IOException;
import java.net.MalformedURLException;
import java.net.URL;
import java.nio.channels.Channels;
import java.nio.channels.FileChannel;
import java.nio.channels.ReadableByteChannel;
import java.util.ArrayList;
import java.util.List;
import java.util.Optional;

public abstract class ArtifactRepository extends Thread {

    private static final int DELAY = 1000 * 5;

    public ArtifactRepository(){
        start();
    }

    public abstract List<Release> getReleases();

    public abstract void onRetrieve(List<Artifact> artifacts);

    public void run(){
        while (true){
            List<Release> releases = getReleases();

            List<Artifact> artifacts = new ArrayList<>();

            if(releases != null){
                for(Release release : releases){
                    File assetFolder = new File("artifacts/"+release.getProject().getGithubRepository()+"/"+release.getUniqueTag());

                    artifacts.add(new Artifact(release, assetFolder));

                    if(assetFolder.mkdirs()){
                        for(Asset all : release.getAssets()){
                            try {
                                Optional<String> githubToken = release.getProject().getGithubToken();
                                File destination = new File(assetFolder.getAbsolutePath()+"/"+all.getName());

                                destination.createNewFile();

                                // Downloading Process
                                HttpsURLConnection connection = (HttpsURLConnection) new URL(all.getUrl()).openConnection();
                                connection.setRequestProperty("Accept", "application/octet-stream");
                                githubToken.ifPresent(s -> connection.setRequestProperty("Authorization", "token " + s));
                                ReadableByteChannel uChannel = Channels.newChannel(connection.getInputStream());
                                FileOutputStream foStream = new FileOutputStream(destination);
                                FileChannel fChannel = foStream.getChannel();
                                fChannel.transferFrom(uChannel, 0, Long.MAX_VALUE);
                                uChannel.close();
                                foStream.close();
                                fChannel.close();
                            } catch (IOException e) {
                                e.printStackTrace();
                            }
                        }
                    }
                }
            }

            onRetrieve(artifacts);

            try {
                Thread.sleep(DELAY);
            } catch (InterruptedException e) {
                e.printStackTrace();
            }
        }
    }

}
