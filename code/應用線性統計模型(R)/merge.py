import pandas as pd

file_path = r"C:\Users\User\Downloads\CH08PR16.txt"
file_path_1 = r"C:\Users\User\Downloads\CH01PR19.txt"


data = pd.read_csv(file_path, delimiter='\t')
data2 = pd.read_csv(file_path_1, delimiter='\t')
print(data2.iloc[:, 0])

df = pd.DataFrame()


df[['A', 'B']] = data2.iloc[:, 0].str.split('    ', expand=True)
print(df.shape)
print(data.shape)
merge = pd.concat([df, data], axis=1)
print(merge.shape)
print(merge)

merge.to_csv(r"C:\Users\User\OneDrive\Desktop\應用線性統計模型作業\merge.txt",
             sep="\t", index=False)
